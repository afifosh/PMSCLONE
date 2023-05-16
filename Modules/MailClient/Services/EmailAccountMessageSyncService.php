<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Services;

use Illuminate\Database\Eloquent\Collection as DatabaseCollection;
use Illuminate\Support\Facades\Log;
use MediaUploader;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Mail\ContentDecoder;
use Modules\Core\Resource\AssociatesResources;
use Modules\MailClient\Client\AbstractMessage;
use Modules\MailClient\Client\Contracts\AttachmentInterface;
use Modules\MailClient\Concerns\InteractsWithEmailMessageAssociations;
use Modules\MailClient\Events\EmailAccountMessageCreated;
use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Models\EmailAccountMessage;
use Plank\Mediable\Exceptions\MediaUploadException;

class EmailAccountMessageSyncService
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
     * Get messages for account
     *
     * @param  int  $accountId
     * @return \Modules\MailClient\Models\EmailAccountMessage
     */
    public function create($accountId, AbstractMessage $message, ?array $associations = null)
    {
        $data = $message->toArray();

        $dbMessage = EmailAccountMessage::create(array_merge($data, [
            'email_account_id' => $accountId,
            'is_sent_via_app' => $message->isSentFromApplication(),
            'hash' => $message->getHeader('x-concord-hash')?->getValue(),
        ]));

        $this->persistAddresses($data, $dbMessage);
        $this->persistHeaders($message, $dbMessage);

        $dbMessage->folders()->sync(
            $this->determineMessageDatabaseFolders($message, $dbMessage)
        );

        $this->handleAttachments($dbMessage, $message);

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
                $this->syncAssociationsViaMessageHeaders($dbMessage, $message);
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
     * @param  \Modules\MailClient\Client\AbstractMessage  $message
     * @param  int  $id The account ID
     * @return \Modules\MailClient\Models\EmailAccountMessage
     */
    public function update($message, $id)
    {
        $data = $message->toArray();

        $dbMessage = EmailAccountMessage::find($id);

        $dbMessage->fill($data)->save();

        $this->persistAddresses($data, $dbMessage);
        $this->persistHeaders($message, $dbMessage);
        $this->replaceBodyInlineAttachments($dbMessage, $message);

        $dbMessage->folders()->sync(
            $this->determineMessageDatabaseFolders($message, $dbMessage)
        );

        return $dbMessage;
    }

    /**
     * Create the message addresses
     *
     * @param  array  $data
     * @param  \Modules\MailClient\Models\EmailAccountMessage  $message
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
     * Delete account message(s)
     *
     * @param  int|\Illuminate\Database\Eloquent\Collection  $message
     * @param  null|int  $fromFolderId
     * @return void
     */
    public function delete($message, $fromFolderId = null)
    {
        $service = new EmailAccountMessageService();

        $eagerLoad = ['folders', 'account', 'account.trashFolder'];

        $allAccounts = EmailAccount::with('trashFolder')->get();

        $messages = is_numeric($message) ?
            new DatabaseCollection([EmailAccountMessage::with($eagerLoad)->find($message)]) :
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
                ->each(function ($messages, $accountId) use ($service, $fromFolderId, $allAccounts) {
                    $service->batchMoveTo(
                        $messages,
                        $allAccounts->find($accountId)->trashFolder,
                        $fromFolderId
                    );
                });
        }

        if (isset($queue['delete'])) {
            $service->batchDelete($queue['delete']);
        }
    }

    /**
     * Create message addresses
     *
     * @param  \Modules\MailClient\Models\EmailAccountMessage  $message
     * @param  \Modules\Core\Mail\Headers\AddressHeader  $addresses
     * @param  string  $type
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
     * @param \Modules\MailClient\Client\Contracts\MessageInterface
     * @param  \App\EmailAccountMessage  $dbMessage
     * @return void
     */
    protected function persistHeaders($message, $dbMessage)
    {
        if ($inReplyTO = $message->getHeader('in-reply-to')) {
            $dbMessage->headers()->updateOrCreate([
                'name' => 'in-reply-to',
            ], [
                'name' => 'in-reply-to',
                'value' => $inReplyTO->getValue(),
                'header_type' => $inReplyTO::class,
            ]);
        }

        if ($references = $message->getHeader('references')) {
            $dbMessage->headers()->updateOrCreate([
                'name' => 'references',
            ], [
                'name' => 'references',
                'value' => implode(', ', $references->getIds()),
                'header_type' => $references::class,
            ]);
        }
    }

    /**
     * Determine the message database folders
     * based on the message folder ID's
     *
     * @param  \Modules\MailClient\Client\Contracts\MessageInterface  $imapMessage
     * @param  \Modules\MailClient\Models\EmailAccountMessage  $dbMessage
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
     * @param  \Modules\MailClient\Models\EmailAccountMessage  $message
     * @param  \Modules\MailClient\Client\Contracts\MessageInterface  $imapMessage
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
            ->reject(
                fn ($attachment, $key) => in_array($key, $embeddedAttachments)
            )->values();

        // Store non-embedded attachments
        return $this->storeAttachments($attachments, $dbMessage, 'attachments');
    }

    /**
     * Replace the message body inline attachments with the actual media links
     *
     * @param \Modules\MailClient\Models\EmailAccountMessage
     * @param  \Modules\MailClient\Client\Contracts\MessageInterface  $imapMessage
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
                    if (
                        is_null($media) && $media = $this->storeAttachments($attachment, $dbMessage, 'embedded-attachments')[0] ?? null
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

        $dbMessage->html_body = $imapMessage->getPreviewBody($replaceCallback);
        $dbMessage->save();

        return $embeddedAttachmentsKeys;
    }

    /**
     * Store message attachments
     *
     * @param  \Iluminate\Support\Collection|\Modules\MailClient\Client\Contracts\AttachmentInterface  $attachments
     * @param  \Modules\MailClient\Models\EmailAccountMessage  $message
     * @param  string  $tag
     * @return array
     */
    protected function storeAttachments($attachments, $message, $tag)
    {
        if ($attachments instanceof AttachmentInterface) {
            $attachments = [$attachments];
        }

        $storedMedia = [];
        $allowedExtensions = config('mediable.allowed_extensions');

        foreach ($attachments as $attachment) {
            $tmpFile = tmpfile();

            fwrite(
                $tmpFile,
                ContentDecoder::decode($attachment->getContent(), $attachment->getEncoding())
            );

            try {
                $storedMedia[] = $media = MediaUploader::fromSource($tmpFile)
                    ->toDirectory($message->getMediaDirectory())
                    ->onDuplicateUpdate()
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

        return $storedMedia;
    }

    /**
     * Associate the message if it's reply to
     * the original message the reply is performed to
     *
     * @param \Modules\MailClient\Models\EmailAccountMessage
     * @param \Modules\MailClient\Client\Contracts\MessageInterface
     * @return bool
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
            $this->syncAssociationsViaMessageHeaders($dbMessage, $remoteMessage);

            return true;
        }

        $inReplyTo = $dbMessage->headers->firstWhere('name', 'in-reply-to');
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
                $dbMessage->{Innoclapps::resourceByName($resource)->associateableName()}()->sync($records->pluck('id')->all());
            }

            return true;
        }

        return false;
    }
}
