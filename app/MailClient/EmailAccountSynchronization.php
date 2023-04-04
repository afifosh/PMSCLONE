<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\MailClient;

use Exception;
use App\Enums\SyncState;
use App\Models\EmailAccount;
use GuzzleHttp\Psr7\Response;
use App\Models\EmailAccountFolder;
use App\Models\EmailAccountMessage;
use Illuminate\Support\Facades\Log;
use App\Innoclapps\MailClient\FolderCollection;
use App\Innoclapps\OAuth\EmptyRefreshTokenException;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Innoclapps\Contracts\MailClient\ImapInterface;
use App\Innoclapps\Contracts\MailClient\FolderInterface;
use App\Innoclapps\Contracts\MailClient\MessageInterface;
use App\Contracts\Repositories\EmailAccountFolderRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;
use App\Innoclapps\MailClient\Exceptions\ConnectionErrorException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

abstract class EmailAccountSynchronization extends EmailAccountSynchronizationManager
{
    use QueuesMessagesForDelete;

    /**
     * Determines how often "Processed X of N emails" hint should be added to a log
     */
    const READ_HINT_COUNT = 100;

    /**
     * Determines how many emails can be stored in a database at once
     */
    const DB_BATCH_SIZE = 100;

    /**
     * @var \App\Innoclapps\Contracts\MailClient\ImapInterface
     */
    protected $imap;

    /**
     * @var \App\Innoclapps\MailClient\FolderCollection
     */
    protected $remoteFolders;

    /**
     * @var int
     */
    protected int $processStartTime;

    /**
     * Indicates the minutes the folders should be synced
     *
     * @var int
     */
    protected int $syncFoldersEvery = 60;

    /**
     * Indicates whether changed were performed
     *
     * @var bool
     */
    protected bool $synced = false;

    /**
     * Initialize new EmailAccountSynchronization instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $accounts
     * @param \App\Contracts\Repositories\EmailAccountMessageRepository $messages
     * @param \App\Contracts\Repositories\EmailAccountFolderRepository $folders
     * @param \App\Models\EmailAccount $account
     */
    public function __construct(
        protected EmailAccountRepository $accounts,
        protected EmailAccountMessageRepository $messages,
        protected EmailAccountFolderRepository $folders,
        protected EmailAccount $account,
    ) {
    }

    /**
     * Start account messages synchronization
     *
     * @throws \App\MailClient\Exceptions\SyncFolderTimeoutException
     *
     * @return void
     */
    abstract public function syncMessages();

    /**
     * Start account folders synchronization
     *
     * @return void
     */
    abstract public function syncFolders() : void;

    /**
     * Perform account folders and messages synchronization
     *
     * @return boolean
     */
    public function perform()
    {
        $this->processStartTime = time();

        // Lazy load protection
        $this->account = $this->account->load(['folders.account', 'sentFolder.account', 'trashFolder.account']);

        try {
            // Folders must by synchronized first
            if ($this->shouldSyncFolders()) {
                $this->info('Starting syncing account folders.');
                $this->syncFolders();
                $this->account->setMeta('last-folders-sync', time());
            }

            // Messages should be synced last
            $this->info('Starting syncing account messages.');
            $this->syncMessages();

            // At the end, delete queued messages (if any)
            $this->info('Deleting messages queued for deletition.');
            $this->deleteQueuedMessages();

            $this->info(sprintf(
                'Synchronization for %s performed successfully.',
                $this->account->email
            ));

            // If previously the account required auth and now it's resolved, update the database
            if ($this->account->requires_auth) {
                $this->accounts->setRequiresAuthentication((int) $this->account->id, false);
            }

            return $this->performed();
        } catch (ConnectionErrorException $e) {
            $this->accounts->setRequiresAuthentication((int) $this->account->id);
            Log::debug("Mail account ({$this->account->email}) connection error: {$e->getMessage()}");
            $this->error('Email account synchronization stopped because of failed authentication [' . $e->getMessage() . '].');
            // To broadcast
            $this->synced = true;
        } catch (EmptyRefreshTokenException $e) {
            $this->accounts->setSyncState(
                $this->account->id,
                SyncState::STOPPED,
                'The sync for this email account is disabled because of empty refresh token, try to remove the app from your ' . explode('@', $this->account->email)[1] . ' account connected apps section and re-connect the account again from the Connected Accounts page.'
            );

            $this->error('Email account synchronization stopped because empty refresh token.');
        } catch (IdentityProviderException $e) {
            // Handle account grant error and account deletition after they are connected
            // e.q. G Suite account ads a user with email, the email connected to the CRM
            // but after that the email is deleted, in this case, we need to catch this error and disable
            // the account sync to stop any exceptions thrown each time the synchronizer runs
            $message      = $e->getMessage();
            $responseBody = $e->getResponseBody();

            if ($responseBody instanceof Response) {
                $responseBody = $responseBody->getReasonPhrase();
            }

            if ($responseBody != $message) {
                $message .= ' [' . is_array($responseBody) ?
                    ($responseBody['error_description'] ?? $responseBody['error'] ?? json_encode($responseBody)) :
                    $responseBody . ']';
            }

            $this->accounts->setSyncState(
                $this->account->id,
                SyncState::STOPPED,
                'Email account sync stopped because of an OAuth error, try reconnecting the account. ' . $message
            );

            $this->error('Email account synchronization stopped because identity provider exception.');
        } catch (Exception $e) {
            // Catch any exceptions to prevent stopping account sync for other valid accounts.
            Log::error("An error occured while synchronizing the {$this->account->email} email account. [{$e->getMessage()}]");

            if (! app()->environment('production')) {
                throw $e;
            }
        } finally {
            $this->finished();
        }
    }

    /**
     * Callback for finisnhed synchronization (may finish with errors)
     *
     * @return void
     */
    protected function finished() : void
    {
    }

    /**
     * Check whether the folders should ne synchronized
     *
     * @return boolean
     */
    protected function shouldSyncFolders() : bool
    {
        $lastFoldersSync = $this->account->getMeta('last-folders-sync');

        return ! $lastFoldersSync || (time() > ($lastFoldersSync + ($this->syncFoldersEvery * 60)));
    }

    /**
     * Check whether synchronization class performed any synchronization
     *
     * @return boolean
     */
    public function performed() : bool
    {
        return $this->synced;
    }

    /**
     * Get the account imap client
     *
     * @return \App\Innoclapps\Contracts\MailClient\ImapInterface|\App\Innoclapps\MailClient\Gmail\ImapClient|\App\Innoclapps\MailClient\Outlook\ImapClient|\App\Innoclapps\MailClient\Imap\ImapClient
     */
    public function getImapClient() : ImapInterface
    {
        if (is_null($this->imap)) {
            $this->imap = $this->account->createClient()->getImap();
        }

        return $this->imap;
    }

    /**
     * Process the messages that should be saved
     *
     * @param \Illuminate\Support\Enumerable $messages
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * Pass the folder the messages belongs to in case, unique remote_id is needed
     * e.q. for IMAP account the remote_id may not be unique, as the remote_id
     * is unique per folder
     *
     * @return void
     */
    protected function processMessages($messages, ?EmailAccountFolder $folder = null) : void
    {
        $count     = 0;
        $processed = 0;
        $batch     = [];
        $total     = $messages->count();
        \Log::info('total'.$total);
        $this->info(sprintf('Processing %s messages.', $total));

        foreach ($this->sortMessagesForSync($messages) as $message) {
            $processed++;

            if ($processed % static::READ_HINT_COUNT === 0) {
                $this->info("Processed $processed of $total messages.");
            }

            $count++;
            $batch[] = $message;

            if ($count === static::DB_BATCH_SIZE) {
                $this->saveMessages($batch, $folder);
                $count = 0;
                $batch = [];
            }

            if ($this->isTimeout()) {
                break;
            }
        }

        // Show the last processed hint count
        if ($processed % static::READ_HINT_COUNT !== 0) {
            $this->info("Processed $processed of $total messages.");
        }

        // Save the last massages added to the batch in case of timeout
        // or the DB_BATCH_SIZE is bigger than the actual messages size
        if ($count > 0) {
            $this->saveMessages($batch, $folder);
        }

        $this->cleanUpAfterFolderSyncComplete($folder);
    }

    /**
     * Save messages in database
     *
     * @param array $messages
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * @return void
     */
    protected function saveMessages($messages, ?EmailAccountFolder $folder = null) : void
    {
        foreach ($messages as $message) {
            // Check if message exists in database
            // If exists, we will perform update to this message
            if (! is_null($this->findDatabaseMessageViaRemoteId($message->getId(), $folder))) {
                $this->updateMessage($message, $message->getId(), $folder);
            } else {
                // UPDATE: This can be solved with using the prefer header idtype immutableid
                // see https://learn.microsoft.com/en-us/graph/outlook-immutable-id
                // but we need to write migration script for the previous messages that were created with mutable ID
                // see https://learn.microsoft.com/en-us/graph/outlook-immutable-id#updating-existing-data

                // The message is deleted from the folder but maybe is moved to another folder
                // When moving messages from the application UI, this is handled
                // but when moving messages from outlook UI, we need to determine
                // if it's the same message and update in local database the new remote_id

                // NOTE: if the folder is not active, the messages from the synced and will be removed from the local database
                $queuedForRemoval = $this->getMessageFromDeleteQueue($message->getSubject(), $message->getMessageId());

                // If the message to be created exists in messages queued for removal then message is moved to another folder
                // in local database, we need to keep the same message with the updated values
                if ($queuedForRemoval) {
                    // Update local values with new values
                    $this->messages->updateForAccount($message, $queuedForRemoval->id);
                    $this->info(sprintf('Updated moved message, UID: %s', $message->getId()));
                    $this->synced = true;

                    // Unset as this message won't be deleted from database
                    // because already is updated with the new ID and folders
                    $this->removeMessageFromDeleteQueue($queuedForRemoval->subject, $queuedForRemoval->message_id);
                } else {
                    $this->messages->createForAccount($this->account->id, $message);
                    $this->synced = true;
                }
            }
        }

        $this->cleanUp();
    }

    /**
     * Find database message via the message remote id
     *
     * @param string|int $id
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * @return \App\Models\EmailAccountMessage|null
     */
    protected function findDatabaseMessageViaRemoteId(string|int $id, ?EmailAccountFolder $folder = null) : ?EmailAccountMessage
    {
        return $this->getDatabaseMessages($folder)->firstWhere('remote_id', $id);
    }

    /**
     * Fetch all account database messages
     *
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * @return mixed
     */
    protected function getDatabaseMessages(?EmailAccountFolder $folder = null)
    {
        $columns = ['subject', 'message_id', 'remote_id', 'id'];

        return ($folder ?
                $this->messages->getUidsByFolder($folder->id, $columns) :
                $this->messages->getUidsByAccount($this->account->id, $columns))->eager();
    }

    /**
     * Delete message
     *
     * @param string|int $id
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * @return boolean
     */
    protected function deleteMessage(string|int $id, ?EmailAccountFolder $folder = null) : bool
    {
        if ($dbMessage = $this->findDatabaseMessageViaRemoteId($id, $folder)) {
            // Triggers observers
            $this->messages->delete($dbMessage->id);

            $this->info(sprintf('Removed local message which was remotely removed, UID: %s', $id));
            $this->synced = true;

            return true;
        }

        return false;
    }

    /**
     * Update message
     *
     * @param \App\Innoclapps\Contracts\MailClient\MessageInterface $message
     * @param string|int $id
     * @param \App\Models\EmailAccountFolder|null $folder
     *
     * @return boolean
     */
    protected function updateMessage(MessageInterface $message, string|int $id, ?EmailAccountFolder $folder = null) : bool
    {
        // Message updated, update it in our local database too
        // e.q. can be used for draft updates messages and also
        // update properties like isRead etc...
        if ($dbMessage = $this->findDatabaseMessageViaRemoteId($id, $folder)) {
            $updatedMessage = $this->messages->updateForAccount($message, $dbMessage->id);

            if ($updatedMessage->isDirty()) {
                $this->info(sprintf('Updated message, UID: %s', $id));
                $this->synced = true;

                return true;
            }
        }

        return false;
    }

    /**
     * Get the account remote folders
     *
     * @return \App\Innoclapps\MailClient\FolderCollection
     */
    protected function getFolders() : FolderCollection
    {
        if (is_null($this->remoteFolders)) {
            $this->remoteFolders = $this->getImapClient()->getFolders();
        }

        return $this->remoteFolders;
    }

    /**
     * Find remote folder by a given database folder
     *
     * @param \App\Models\EmailAccountFolder $folder
     *
     * @return \App\Innoclapps\Contracts\MailClient\FolderInterface|\App\Innoclapps\MailClient\Gmail\Folder|\App\Innoclapps\MailClient\Outlook\Folder|\App\Innoclapps\MailClient\Imap\Folder|null
     */
    protected function findFolder(EmailAccountFolder $folder) : ?FolderInterface
    {
        return $this->getFolders()->find($folder->identifier());
    }

    /**
     * Find a database folder of a given remote folder
     *
     * @param \App\Innoclapps\Contracts\MailClient\FolderInterface $remoteFolder
     *
     * @return \App\Models\EmailAccountFolder|null
     */
    protected function findDatabaseFolder(FolderInterface $remoteFolder) : ?EmailAccountFolder
    {
        return $this->account->folders->findByIdentifier($remoteFolder->identifier());
    }

    /**
     * Remove remotely removed folders
     *
     * @return void
     */
    protected function removeRemotelyRemovedFolders() : void
    {
        $this->checkRemovedFolders(fn ($folder) => is_null($this->findFolder($folder)));
    }

    /**
     * Check remotely removed folders
     *
     * Common function for all sync managers because the same logic is used
     * on all synchronization classes, the class just needs to provide a callback
     * whether to delete the folder
     *
     * @param \Closure $deleteCallback Provide a callback whether to delete the folder or not
     *
     * @return void
     */
    protected function checkRemovedFolders(\Closure $deleteCallback) : void
    {
        $this->info('Starting synchronization of remotely removed folders.');

        foreach ($this->account->folders as $databaseFolder) {
            if ($deleteCallback($databaseFolder) === true) {
                $this->deleteFolder($databaseFolder);
            }
        }
    }

    /**
     * Delete folder from database
     *
     * @param \App\Models\EmailAccountFolder $folder
     *
     * @return void
     */
    protected function deleteFolder(EmailAccountFolder $folder) : void
    {
        $this->folders->delete($folder->id);

        // Remove the account folder once it's removed from database
        // so it doesn't interfere with the synchronization e.q. trying
        // to sync a folder which is removed
        $this->account->folders = $this->account->folders
            ->reject(fn ($dbFolder) => $dbFolder->id === $folder->id)->values();

        $this->info(sprintf(
            'Removed remotely deleted folder from local database, folder name: %s',
            $folder->name
        ));

        $this->synced = true;
    }

    /**
     * The messages must be synchronized from oldest to newest
     * as we can associate the replies associations and
     * possibility in future, create threads
     *
     * @param \Illuminate\Support\Enumerable $messages
     *
     * @return \Illuminate\Support\Enumerable
     */
    protected function sortMessagesForSync($messages)
    {
        return $messages->sortBy(fn ($message) => $message->getDate())->values();
    }
}
