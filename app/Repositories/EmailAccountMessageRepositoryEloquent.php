<?php

namespace App\Repositories;

use MediaUploader;
use Illuminate\Support\Arr;
use App\Models\MessageLinksClick;
use App\Models\EmailAccountMessage;
use Illuminate\Support\Facades\Log;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Mail\ContentDecoder;
use App\Events\EmailAccountMessageCreated;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\MailClient\AbstractMessage;
use App\Innoclapps\Resources\AssociatesResources;
use Plank\Mediable\Exceptions\MediaUploadException;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Contracts\Repositories\EmailAccountFolderRepository;
use App\Innoclapps\Contracts\MailClient\AttachmentInterface;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Support\Concerns\InteractsWithEmailMessageAssociations;
use Exception;
use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Plank\Mediable\Facades\MediaUploader as FacadesMediaUploader;

class EmailAccountMessageRepositoryEloquent extends AppRepository implements EmailAccountMessageRepository
{
    use AssociatesResources,
        InteractsWithEmailMessageAssociations;

    /**
     * Message addresses headers and relations
     *
     * @var array
     */
    protected $addresses = ['from', 'to', 'cc', 'bcc', 'replyTo', 'sender'];

    /**
     * @var \App\Contracts\Repositories\EmailAccountFolderRepository
     */
    protected $folderRepository;

    /**
     * Searchable fields
     *
     * @var array
     */
    protected static $fieldSearchable = [
        'subject'      => 'like',
        'text_body'    => 'like',
        'html_body'    => 'like',
        'from.address' => 'like',
        'from.name'    => 'like',
    ];

    /**
     * Cache account folders
     *
     * When creating a lot messages we don't want
     *
     * thousands of queries to be executed
     *
     * @var array
     */
    protected $cachedAccountFolders = [];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return EmailAccountMessage::class;
    }

    public function recordOpen(EmailAccountMessage $message)
    {
        $message->opens++;

        if (! $message->opened_at) {
            $message->opened_at = now();
        }

        $message->save();

        return $message;
    }

    public function recordLinkClick(EmailAccountMessage $message, $url)
    {
        $click = MessageLinksClick::where('url', $url)
            ->where('message_id', $message->id)
            ->first();

        if ($click) {
            $click->total++;
            $click->save();
        } else {
            $message->linksClicks()->create([
                'url' => $url,
            ]);
        }

        // If no opened at but has a clicked event then we can assume that it was in fact opened, the tracking pixel may have been blocked
        if (! $message->opened_at) {
            $message->opened_at = now();
        }

        if (! $message->clicked_at) {
            $message->clicked_at = now();
        }

        $message->clicks++;

        $message->save();

        return $message;
    }

    /**
     * Find message by the given hash
     *
     * @param string $hash
     *
     * @return \App\Models\EmailAccountMessage|null
     */
    public function findByHash(string $hash) : ?EmailAccountMessage
    {
        return $this->limit(1)->findWhere(['hash' => $hash])->first();
    }

    /**
     * Find message for account
     *
     * @param int $messageId
     * @param int $accountId
     *
     * @return \App\Models\EmailAccountMessage
     */
    public function findForAcount($messageId, $accountId)
    {
        return tap($this->scopeQuery(
            fn ($query) => $query->where('email_account_id', $accountId)
        )->find($messageId), fn () => $this->resetScope());
    }

    /**
     * Get messages for account
     *
     * @param int $accountId
     * @param \App\Innoclapps\MailClient\AbstractMessage $message
     * @param array|null $associations
     *
     * @return \App\Models\EmailAccountMessage
     */
    public function createForAccount($accountId, AbstractMessage $message, ?array $associations = null)
    {
        $data = $message->toArray();

        $dbMessage = parent::create(array_merge($data, [
            'email_account_id' => $accountId,
            'is_sent_via_app'  => $message->isSentFromApplication(),
            'hash'             => $message->getHeader('x-concord-hash')?->getValue(),
        ]));

        $this->persistAddresses($data, $dbMessage);
        $this->persistHeaders($message, $dbMessage);
        $this->handleAttachments($dbMessage, $message);

        $dbMessage->folders()->sync(
            $this->determineMessageDatabaseFolders($message, $dbMessage)
        );

        // When associations are passed manually
        // this means that the user can manually associate the message
        // to resources, in this case, we use the user associations
        // after that for each reply from the client for this messages, the user
        // selected associations are used.
        if ($associations) {
            $this->attachAssociations('emails', $dbMessage->getKey(), $associations);
        } else {
            if ($dbMessage->isReply()) {
                $this->syncAssociationsWhenReply($dbMessage, $message);
            } else {
                // If the message is queued, we need to fetch the associations from
                // the headers and sync with the actual associations
                $this->syncAssociationsViaMessageHeaders($dbMessage->id, $message);
            }
        }

        event(new EmailAccountMessageCreated($dbMessage, $message));

        return $dbMessage;
    }

    /**
     * Update a message for a given account
     *
     * NOTE: This functions does not syncs attachments
     *
     * @param \App\Innoclapps\MailClient\AbstractMessage $message
     * @param int $id The account ID
     *
     * @return \App\Models\EmailAccountMessage
     */
    public function updateForAccount($message, $id)
    {
        $data      = $message->toArray();
        $dbMessage = parent::update($data, $id);

        $this->persistAddresses($data, $dbMessage);
        $this->persistHeaders($message, $dbMessage);
        $this->replaceBodyInlineAttachments($dbMessage, $message);

        $dbMessage->folders()->sync(
            $this->determineMessageDatabaseFolders($message, $dbMessage)
        );

        return $dbMessage;
    }

    /**
     * Delete account message(s)
     *
     * @param int|\Illuminate\Database\Eloquent\Collection $message
     * @param null|int $fromFolderId
     * @return void
     */
    public function deleteForAccount($message, $fromFolderId = null)
    {
        $eagerLoad   = ['folders', 'account', 'account.trashFolder'];
        $allAccounts = resolve(EmailAccountRepository::class)->with('trashFolder')->all();

        $messages = is_numeric($message) ?
            new DatabaseCollection([$this->with($eagerLoad)->find($message)]) :
            $message->loadMissing($eagerLoad);

        $queue = $messages->mapToGroups(function ($message) {
            // When message is in the trash folder, we will parmanently delete
            // this message from the remote server
            if ($message->folders->find($message->account->trashFolder)) {
                return ['delete' => $message];
            }

            return ['move' => $message];
        });

        if (isset($queue['move'])) {
            $queue['move']->groupBy('email_account_id')
                ->each(function ($messages, $accountId) use ($fromFolderId, $allAccounts) {
                    $this->batchMoveTo(
                        $messages,
                        $allAccounts->find($accountId)->trashFolder,
                        $fromFolderId
                    );
                });
        }

        if (isset($queue['delete'])) {
            $this->batchDelete($queue['delete']);
        }
    }

    /**
     * Batch move messages to a given folder
     *
     * @param \Illuminate\Database\Eloquent\Collection $messages
     * @param int $to
     * @param null|int $from
     *
     * @return void
     */
    public function batchMoveTo($messages, $to, $from = null)
    {
        $messages->loadMissing('folders');

        $allAccounts = resolve(EmailAccountRepository::class)->with('oAuthAccount')->all();
        $allFolders  = $this->getFolderRepository()->with('account')->all();
        $to          = $allFolders->find($to);

        $messagesByAccount = $messages->groupBy('email_account_id');

        foreach ($messagesByAccount as $accountId => $accountMessages) {
            $messagesByFromFolder = $accountMessages->groupBy(
                fn ($message) => $from ? $message->folders->find($from)->id : $message->folders->first()->id
            )->reject(
                fn ($messages, $fromFolderId) => $allFolders->find($fromFolderId)->is($to) || $to->support_move === false
            );

            if ($messagesByFromFolder->isNotEmpty()) {
                $client        = $allAccounts->find($accountId)->getClient();
                $remoteFolders = $client->getFolders();

                // We will use the first message to get the FROM folder
                // as the messages are grouped by FROM, for the rest messages
                // the FROM folder will be the same
                $from = $allFolders->find($messagesByFromFolder->keys()->first());

                foreach ($messagesByFromFolder as $messages) {
                    $maps = $client->batchMoveMessages(
                        $messages->pluck('remote_id')->all(),
                        $remoteFolders->find($to->identifier()),
                        $remoteFolders->find($from->identifier())
                    );

                    foreach ($messages as $message) {
                        // Maps of old => new values exists, in this case, update the current
                        // messages with the new remote_id's to avoid any syncing errors
                        if (is_array($maps)) {
                            // This will help to not delete the message from database
                            // because it's removed
                            if (array_key_exists($message->remote_id, $maps)) {
                                $this->update(['remote_id' => $maps[$message->remote_id]], $message->id);
                            }
                        }

                        // Since messages can belong to multiple folders e.q. for Gmail
                        // We need to remove the FROM folder from the current folders
                        // and push the new folder
                        $message->folders()
                            ->sync(
                                $message->folders->reject(
                                    fn ($folder) => $folder->id == $from->id
                                )->push($to)
                            );
                    }
                }
            }
        }
    }

    /**
     * Parmanently delete given messages
     *
     * @param \Illuminate\Support\Collection
     *
     * @return void
     */
    public function batchDelete($messages)
    {
        $allAccounts       = resolve(EmailAccountRepository::class)->all();
        $messagesByAccount = $messages->groupBy('email_account_id');

        $messagesByAccount->each(function ($messages, $accountId) use ($allAccounts) {
            $account = $allAccounts->find($accountId);
            $client = $account->getClient();
            $client->setTrashFolder($client->getFolders()->find($account->trashFolder->identifier()))
                ->batchDeleteMessages($messages->pluck('remote_id')->all());

            $messages->each(function ($message) {
                $this->delete($message->id);
            });
        });
    }

    /**
     * Mark a message as read
     *
     * @param int $id
     * @param int|null $folderId
     *
     * @return boolean
     */
    public function markAsRead($id, $folderId = null)
    {
        $message = $this->find($id);

        if ($message->is_read) {
            return false;
        }

        $folders = $folderId ?
        [$this->getFolderRepository()->with('account')->find($folderId)] :
        $message->folders->loadMissing('account');

        foreach ($folders as $folder) {
            $message->account->createClient()->getMessage(
                $message->remote_id,
                $folder->identifier()
            )->markAsRead();
        }

        return $this->update(['is_read' => true], $id);
    }

    /**
     * Mark a message as unread
     *
     * @param int $id
     * @param int|null $folderId
     *
     * @return boolean
     */
    public function markAsUnread($id, $folderId=null)
    {
        $message = $this->find($id);

        if (! $message->is_read) {
            return false;
        }

        $folders = $folderId ?
        [$this->getFolderRepository()->with('account')->find($folderId)] :
        $message->folders->loadMissing('account');

        foreach ($folders as $folder) {
            $message->account->createClient()->getMessage(
                $message->remote_id,
                $folder->identifier()
            )->markAsUnread();
        }

        return $this->update(['is_read' => false], $id);
    }

    /**
     * Batch mark a messages as read
     *
     * @param \Illuminate\Support\Collection $messages
     * @param int $accountId
     * @param int $folderId
     *
     * @return void
     */
    public function batchMarkAsRead($messages, $accountId, $folderId)
    {
        $account = resolve(EmailAccountRepository::class)->find($accountId);

        $messages = $messages->reject(fn ($message) => $message->is_read === true)->values();

        if ($messages->isEmpty()) {
            return;
        }

        $account->createClient()->batchMarkAsRead(
            $messages->pluck('remote_id')->all(),
            $this->getFolderRepository()->find($folderId)->identifier()
        );

        $this->scopeQuery(
            fn ($query) => $query->whereIn('id', $messages->pluck('id')->all())
        )->massUpdate(['is_read' => true]);

        $this->resetScope();
    }

    /**
     * Mark a message as read
     *
     * @param \Illuminate\Support\Collection $messages
     * @param int $accountId
     * @param int $folderId
     *
     * @return boolean
     */
    public function batchMarkAsUnread($messages, $accountId, $folderId)
    {
        $account = resolve(EmailAccountRepository::class)->find($accountId);

        $messages = $messages->reject(fn ($message) => $message->is_read === false)->values();

        if ($messages->isEmpty()) {
            return;
        }

        $account->createClient()->batchMarkAsUnread(
            $messages->pluck('remote_id')->all(),
            $this->getFolderRepository()->find($folderId)->identifier()
        );

        $this->scopeQuery(
            fn ($query) => $query->whereIn('id', $messages->pluck('id')->all())
        )->massUpdate(['is_read' => false]);

        $this->resetScope();
    }

    /**
     * Mark messages as read by remote ids
     *
     * @param int $folderId The folder id to not prevent conflicts in case of same remote uid's
     * @param array $remoteIds
     *
     * @return boolean
     */
    public function markAsReadByRemoteIds($folderId, array $remoteIds)
    {
        return tap($this->whereInRemoteIds($remoteIds, function ($query) use ($folderId) {
            return $query->whereHas('folders', function ($subQuery) use ($folderId) {
                return $subQuery->where('folder_id', $folderId);
            });
        })->massUpdate(['is_read' => 1]), function () {
            $this->resetScope();
        });
    }

    /**
     * Mark messages as unread by remote ids
     *
     * @param int $folderId The folder id to not prevent conflicts in case of same remote uid's
     * @param array $remoteIds
     *
     * @return boolean
     */
    public function markAsUnreadByRemoteIds($folderId, array $remoteIds)
    {
        return tap($this->whereInRemoteIds($remoteIds, function ($query) use ($folderId) {
            return $query->whereHas('folders', function ($subQuery) use ($folderId) {
                return $subQuery->where('folder_id', $folderId);
            });
        })->massUpdate(['is_read' => 0]), function () {
            $this->resetScope();
        });
    }

    /**
     * Get database uids for a given folder
     *
     * @param int $folderId
     * @param array $columns
     *
     * @return \Illuminate\Collections\LazyCollection
     */
    public function getUidsByFolder($folderId, $columns = ['remote_id'])
    {
        return $this->columns($columns)->whereFolder($folderId)->cursor();
    }

    /**
     * Customer helper function for where queries for folder
     *
     * NOTE: Do not use it with massUpdate repository method as massUpdate
     * created new model before the query
     *
     * @param int $folderId
     *
     * @return static
     */
    public function whereFolder($folderId)
    {
        return $this->whereHas('folders', function ($query) use ($folderId) {
            return $query->where('folder_id', $folderId);
        });
    }

    /**
     * Get database uids for a given folder
     *
     * @param int $accountId
     * @param array $columns
     *
     * @return \Illuminate\Collections\LazyCollection
     */
    public function getUidsByAccount($accountId, $columns = ['remote_id'])
    {
        return tap($this->columns($columns)
            ->scopeQuery(function ($query) use ($accountId) {
                return $query->where('email_account_id', $accountId);
            })->cursor(), function () {
                $this->resetScope();
            });
    }

    /**
     * Find the last synced uid by folder id
     * This is applied only for IMAP account as their last uid
     * may be guaranteed to be integer
     *
     * @param int $folderId
     *
     * @return null|int
     */
    public function getLastUidByForImapAccountByFolder($folderId)
    {
        $result = $this->columns('remote_id')
            ->whereFolder($folderId)
            ->orderBy(\DB::raw('CAST(remote_id AS UNSIGNED)'), 'DESC')
            ->first();

        return $result->remote_id ?? null;
    }

    /**
     * Create the message addresses
     *
     * @param array $data
     * @param \App\Models\EmailAccountMessage $message
     *
     * @return void
     */
    protected function persistAddresses($data, $message)
    {
        // Delete the existing addresses
        // Below we will re-create them
        $message->addresses()->delete();

        foreach ($this->addresses as $type) {
            if (is_null($data[$type])) {
                continue;
            }

            $this->createAddresses($message, $data[$type], $type);
        }
    }

    /**
     * Create message addresses
     *
     * @param \App\Models\EmailAccountMessage $message
     * @param \App\Innoclapps\Mail\Headers\AddressHeader $addresses
     * @param string $type
     *
     * @return void
     */
    protected function createAddresses($message, $addresses, $type)
    {
        foreach ($addresses->getAll() as $address) {
            $message->addresses()->create(array_merge($address, [
                'address_type' => $type,
            ]));
        }
    }

    /**
     * Persist the message header in database
     *
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface
     * @param \App\EmailAcccountMessage $dbMessage
     *
     * @return void
     */
    protected function persistHeaders($message, $dbMessage)
    {
        if ($inReplyTO = $message->getHeader('in-reply-to')) {
            $dbMessage->headers()->updateOrCreate([
                'name' => 'in-reply-to',
            ], [
                'name'        => 'in-reply-to',
                'value'       => $inReplyTO->getValue(),
                'header_type' => $inReplyTO::class,
            ]);
        }

        if ($references = $message->getHeader('references')) {
            $dbMessage->headers()->updateOrCreate([
                'name' => 'references',
            ], [
                'name'        => 'references',
                'value'       => implode(', ', $references->getIds()),
                'header_type' => $references::class,
            ]);
        }
    }

    /**
     * Determine the message database folders
     * based on the message folder ID's
     *
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface $imapMessage
     * @param \App\Models\EmailAccountMessage $dbMessage
     *
     * @return array
     */
    protected function determineMessageDatabaseFolders($imapMessage, $dbMessage)
    {
        if (isset($this->cachedAccountFolders[$dbMessage->email_account_id])) {
            $folders = $this->cachedAccountFolders[$dbMessage->email_account_id];
        } else {
            $folders = $this->cachedAccountFolders[$dbMessage->email_account_id] = $dbMessage->account->folders;
            // For identifier looping in EmailAccountFolderCollection, avoids lazy loading protection
            $folders->loadMissing('account');
        }

        return $folders->findWhereIdentifierIn($imapMessage->getFolders())->pluck('id')->all();
    }

    /**
     * Save the message attachments
     *
     * @param \App\Models\EmailAccountMessage $message
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface $imapMessage
     *
     * @return array
     */
    protected function handleAttachments($dbMessage, $imapMessage)
    {
        // Store embedded attachments with embedded-attachments tag
        // We will cast as embedded/inline attachments only the attachments which
        // exists in the message body with src="cid_CONTENT_ID"
        $embeddedAttachments = $this->replaceBodyInlineAttachments($dbMessage, $imapMessage);

        // Remove the embedded attachments as they are stored with different tag
        $attachments = $imapMessage->getAttachments()
            ->reject(function ($attachment, $key) use ($embeddedAttachments) {
                return in_array($key, $embeddedAttachments);
            })->values();

        // Store non-embedded attachments
        return $this->storeAttachments($attachments, $dbMessage, 'attachments');
    }

    /**
     * Replace the message body inline attachments with the actual media links
     *
     * @param \App\Models\EmailAccountMessage
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface $imapMessage
     *
     * @return array
     */
    protected function replaceBodyInlineAttachments($dbMessage, $imapMessage)
    {
        $embeddedAttachmentsKeys = [];

        // We will provide a closure to the getPreviewBody method
        // to provide a custom content for the replace
        $replaceCallback = function ($file) use ($dbMessage, $imapMessage, &$embeddedAttachmentsKeys) {
            foreach ($imapMessage->getAttachments() as $key => $attachment) {
                if ($attachment->getContentId() === $file->getContentId()) {
                    // Check if the attachment with this content-id is already stored
                    // if yes, we will return the same media preview url
                    // Useful e.q. on update when the message already exists and
                    // we are trying to update it
                    $media = $dbMessage->inlineAttachments->first(function ($inlineMedia) use ($file) {
                        return $inlineMedia->getMeta('content-id') === $file->getContentId();
                    });

                    // When no media with this content-id found, we will create
                    // the media as embedded attachment and will set the meta content-id
                    if (is_null($media) &&
                    $media = $this->storeAttachments($attachment, $dbMessage, 'embedded-attachments')[0] ?? null
                    ) {
                        $media->setMeta('content-id', $file->getContentId());
                    }

                    if ($media) {
                        $embeddedAttachmentsKeys[] = $key;

                        return $media->previewPath();
                    }
                }
            }
        };

        $this->update(['html_body' => $imapMessage->getPreviewBody($replaceCallback)], $dbMessage->id);

        return $embeddedAttachmentsKeys;
    }

    /**
     * Store message attachments
     *
     * @param \Iluminate\Support\Collection|\App\Innoclapps\Contracts\MailClient\AttachmentInterface $attachments
     * @param \App\Models\EmailAccountMessage $message
     * @param string $tag
     *
     * @return array
     */
    protected function storeAttachments($attachments, $message, $tag)
    {
        if ($attachments instanceof AttachmentInterface) {
            $attachments = [$attachments];
        }

        $storedMedias      = [];
        $allowedExtensions = config('app.allowed_extensions');

        foreach ($attachments as  $attachment) {
            $tmpFile = tmpfile();
            fwrite(
                $tmpFile,
                ContentDecoder::decode($attachment->getContent(), $attachment->getEncoding())
            );

            try {
                $storedMedias[] = $media = FacadesMediaUploader::fromSource($tmpFile)
                    ->toDirectory($message->getMediaDirectory())
                    ->useFilename($filename = pathinfo($attachment->getFileName(), PATHINFO_FILENAME))
                    // Allow any extension
                    ->setAllowedExtensions(array_unique(
                        array_merge($allowedExtensions, [pathinfo($attachment->getFileName(), PATHINFO_EXTENSION)])
                    ))
                    ->upload();
                $message->attachMedia($media, [$tag]);
            } catch (MediaUploadException $e) {
                Log::debug(
                    sprintf(
                        'Failed to store mail message [ID: %s] attachment, filename: %s, exception message: %s',
                        $message->getKey(),
                        $filename,
                        $e->getMessage()
                    ),
                );

                continue;
            } finally {
                // If the media package did not closed the file, close it
                // As per the tests, it looks like the package closes the tmpfile
                if (is_resource($tmpFile)) {
                    fclose($tmpFile);
                }
            }
        }

        return $storedMedias;
    }

    /**
     * Associate the message if it's reply to
     * the original message the reply is performed to
     *
     * @param \App\Models\EmailAccountMessage
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface
     *
     * @return boolean
     */
    protected function syncAssociationsWhenReply($dbMessage, $remoteMessage)
    {
        // If the message is sent from the application,
        // we will use the headers to associate the selected
        // associations, otherwise, we will use the dependent message
        // If this method is hit, this means that the message was queued for
        // sync and was not inserted in database when the user click send, hence
        // the associations were not saved in database
        if ($remoteMessage->isSentFromApplication()) {
            $this->syncAssociationsViaMessageHeaders($dbMessage->id, $remoteMessage);

            return true;
        }

        $inReplyTo  = $dbMessage->headers->firstWhere('name', 'in-reply-to');
        $references = $dbMessage->headers->firstWhere('name', 'references');

        // First check the in-reply to header as it's the most applicable header
        if ($inReplyTo) {
            $inReplyToMessageId = $inReplyTo->mapped->getValue();
        } elseif ($references) {
            // If in-reply-to header is not set, let's check the references
            // and get the last reference, probably the mail client set the message that replied as reference
            $referencesIds = $references->mapped->getIds();

            if (count($referencesIds) === 0 || empty($references->value)) {
                return false;
            }

            $inReplyToMessageId = $referencesIds[array_key_last($referencesIds)];
        } else {
            return false;
        }

        $inReplyToDbMessage = EmailAccountMessage::query()
            ->whereFullText('message_id', $inReplyToMessageId)
            ->where('email_account_id', $dbMessage->email_account_id)
            ->first();

        if ($inReplyToDbMessage) {
            foreach ($inReplyToDbMessage->associatedResources() as $resource => $records) {
                $this->sync(
                    $dbMessage->id,
                    Innoclapps::resourceByName($resource)->associateableName(),
                    $records->pluck('id')->all()
                );
            }

            return true;
        }

        return false;
    }

    /**
     * Apply where in remote ids
     *
     * @see https://stackoverflow.com/questions/53683774/eloquent-delete-sqlstate22007-invalid-datetime-format-1292-truncated-incor
     *
     * @param array $remoteIds
     * @param null|\Closure $callback
     *
     * @return static
     */
    public function whereInRemoteIds($remoteIds, $callback = null)
    {
        return $this->scopeQuery(function ($query) use ($remoteIds, $callback) {
            if ($callback) {
                $query = $callback($query);
            }

            return $query->whereIn('remote_id', Arr::valuesAsString($remoteIds));
        });
    }

    /**
     * Get the folder repository
     */
    public function getFolderRepository() : EmailAccountFolderRepository
    {
        if (! is_null($this->folderRepository)) {
            return $this->folderRepository;
        }

        return $this->folderRepository = resolve(EmailAccountFolderRepository::class);
    }

    /**
     * Boot the repository
     *
     * @return void
     */
    public static function boot()
    {
        static::deleting(function ($model, $repository) {
            $repository->purge($model);
        });
    }

    /**
     * Purge the message data
     *
     * @param \App\Models\EmailAccountMessage $message
     *
     * @return void
     */
    protected function purge($message)
    {
        foreach (['contacts', 'companies', 'folders'] as $relation) {
            tap($message->{$relation}(), function ($query) {
                if ($query->getModel()->usesSoftDeletes()) {
                    $query->withTrashed();
                }

                $query->detach();
            });
        }
    }

    /**
     * The relations that are required for the responsee
     *
     * @return array
     */
    protected function eagerLoad()
    {
        return [
                'headers',
                'from',
                'sender',
                'to',
                'cc',
                'bcc',
                'replyTo',
                'attachments',
                'folders',
                'account',
                'account.folders' => fn ($query) => $query->withCount([
                    'messages as unread_count' => fn ($query) => $query->where('is_read', false),
                ]),
                'contacts.nextActivity',
                'companies.nextActivity',
            ];
    }
}
