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

namespace Modules\MailClient\Synchronization;

use Carbon\Carbon;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Support\Str;

class GmailEmailAccountSynchronization extends EmailAccountIdBasedSynchronization
{
    /**
     * The history meta key
     */
    const HISTORY_META_KEY = 'historyId';

    /**
     * Mode for the sync process
     *
     * @var string chill|force
     */
    protected $mode = self::FORCE_MODE;

    /**
     * Limit the Gmail messages request
     */
    protected int $limit = 150;

    /**
     * Start account messages synchronization
     */
    public function syncMessages(): void
    {
        foreach ($this->account->folders->active() as $folder) {
            // Perform check in the loop in case rate limit exceeded while looping through the folders
            if ($this->isAccountRateLimitExceeded()) {
                $this->error(
                    sprintf(
                        'Skipping sync for folder %s, rescheduled for %s, account user-rate limit exceeded.',
                        $folder->name,
                        $this->account->getMeta('_continue_sync_after')
                    )
                );

                continue;
            }

            if ($currentHistoryId = $folder->getMeta(static::HISTORY_META_KEY)) {
                $this->syncFromHistoryId($currentHistoryId, $folder);
            } else {
                $this->syncAll($folder);
            }
        }
    }

    /**
     * Sync account via Gmail history id data
     *
     * @param  int  $currentHistoryId
     * @param  \Modules\MailClient\Models\EmailAccountFolder  $folder
     * @return void
     */
    protected function syncFromHistoryId($currentHistoryId, $folder)
    {
        $this->info(sprintf('Performing sync for folder %s via history id.', $folder->name));

        try {
            [
                'messages' => $messages,
                'newHistoryId' => $newHistoryId,
                'deleted' => $deletedMessages
            ] = $this->retrieveViaHistoryId($currentHistoryId, $folder);

            // Update/create for messages
            // Handles all three methods, messagesAded, labelsAdded, labelsRemoved
            $filtered = $messages->reject(
                fn ($history) => in_array($history->getMessage()->getId(), $deletedMessages)
            )
            // The messages may be duplicated multiple times in the Google history data
                ->unique(fn ($history) => $history->getMessage()->getId())
                ->map(fn ($history) => $history->getMessage())->values();

            // We will fetch each unique message via batch request so we can perform update or insert with the new data
            // The batch will also check for any messages which are not found and will remove them from the array
            $this->processMessages(
                $this->excludeSystemMailables($this->getImapClient()->batchGetMessages($filtered))
            );

            if (isset($newHistoryId)) {
                $folder->setMeta(static::HISTORY_META_KEY, $newHistoryId);
            }
        } catch (GoogleServiceException $e) {
            /*
            * A historyId is typically valid for at least a week, but in some rare circumstances
            * may be valid for only a few hours.
            *
            * If you receive an HTTP 404 error response, your application should perform a full sync.
            *
            * @link https://developers.google.com/gmail/api/v1/reference/users/history/list#startHistoryId
            */
            if ($e->getCode() == 404) {
                return $this->syncAll($folder);
            } elseif ($this->isRateLimitExceededException($e)) {
                $retryAfter = $this->setAccountSyncAfterFlag($e);

                $this->error(sprintf(
                    'Skipping sync for folder %s, rescheduled for %s, account user-rate limit exceeded.',
                    $folder->name,
                    $retryAfter
                ));
            }

            throw $e;
        }
    }

    /**
     * Sync all account messages
     *
     * @param  \Modules\MailClient\Models\EmailAccountFolder  $folder
     * @return void
     */
    protected function syncAll($folder)
    {
        $remoteFolder = $this->findFolder($folder);

        // Trash and spam folders are not synced on the initial sync
        // But we need to get the first history id from the first message so
        // we can store the history id in database as it was synced
        if ($remoteFolder->isTrashOrSpam()) {
            return $this->setFolderHistoryIdFromMessage(
                $folder,
                $this->getInitialMessages($remoteFolder, 1)->first()
            );
        }

        $this->info(sprintf('Performing initial sync for folder %s.', $folder->name));

        $nextPageResult = null;
        // If _continue_sync_token flag is empty, will perform initial sync
        $continueFromPageToken = $folder->getMeta('_continue_sync_token');

        do {
            try {
                // Initial request
                if (! $nextPageResult) {
                    /** @var \Modules\Core\Google\Services\MessageCollection */
                    $result = $this->getInitialMessages($remoteFolder);

                    // Remember the first message as we will set the history id
                    // after the messages are processed and the system mailables excluded
                    // the message token will be saved after all data is saved, it should not
                    // be saved when rate limit exceeded exception is thrown because the sync must continue from where stopped
                    $firstMessage = $result->first();

                    if (! is_null($continueFromPageToken)) {
                        $result->setNextPageToken($continueFromPageToken);
                        /** @var \Modules\Core\Google\Services\MessageCollection */
                        $result = $result->getNextPageResults();
                    }
                } else {
                    /** @var \Modules\Core\Google\Services\MessageCollection */
                    $result = $nextPageResult;
                }

                $this->processMessages($this->excludeSystemMailables($result));
            } catch (GoogleServiceException $e) {
                if ($this->isRateLimitExceededException($e)) {
                    $retryAfter = $this->setAccountSyncAfterFlag($e);
                    $folder->setMeta('_continue_sync_token', $result->getPrevPageToken());

                    $this->error(sprintf(
                        'Skipping sync for folder %s, rescheduled for %s, account user-rate limit exceeded.',
                        $folder->name,
                        $retryAfter
                    ));
                }

                throw $e;
            }
        } while ($nextPageResult = $result->getNextPageResults());

        $this->setFolderHistoryIdFromMessage($folder, $firstMessage ?? null);
        $folder->removeMeta('_continue_sync_token');
    }

    /**
     * Retrieve data via history ID for the given folder
     *
     * @param  int  $currentHistoryId
     * @param  \Modules\MailClient\Models\EmailAccountFolder  $folder
     */
    protected function retrieveViaHistoryId($historyId, $folder): array
    {
        $nextPage = null;
        $deleted = [];
        $messages = collect([]);

        do {
            $historyList = $this->getImapClient()->getHistory($historyId, [
                'maxResults' => $this->limit,
                'pageToken' => $nextPage,
                'labelId' => $folder->remote_id,
            ]);

            foreach ($historyList->getHistory() ?? [] as $history) {
                // First handle all removed messages
                // Remove them from database so we can fetch all messages below in a batch and perform create/update
                foreach ($history->getMessagesDeleted() ?? [] as $message) {
                    $deleted[] = $messageId = $message->getMessage()->getId();

                    $this->deleteMessage($messageId);
                }

                $messages = $messages->merge($history->getMessagesAdded() ?? [])
                    ->merge($history->getLabelsAdded() ?? [])
                    ->merge($history->getLabelsRemoved() ?? []);
            }

            // We need to get the History ID in the first batch
            // so we can know up to which point the sync has been done for this user.
            if (! isset($newHistoryId)) {
                $newHistoryId = $historyList->getHistoryId();
            }
        } while (($nextPage = $historyList->getNextPageToken()));

        return [
            'newHistoryId' => $newHistoryId ?? null,
            'messages' => $messages,
            'deleted' => $deleted,
        ];
    }

    /**
     * Get the initial messages for the for sync
     *
     * @param  \Modules\MailClient\Client\Contracts\FolderInterface  $folder
     * @param  null|int  $limit
     * @return \Illuminate\Support\Collection
     */
    protected function getInitialMessages($folder, $limit = null)
    {
        return $folder->getMessagesFrom(
            $this->account->initial_sync_from->format('Y-m-d H:i:s'),
            $limit ?? $this->limit
        );
    }

    /**
     * We need to get the History ID from the very first existing message
     * so we can know up to which point the sync has been done for this folder.
     *
     * The the database folder history id from the provided message
     * In all cases, the provided message should be the first message
     *
     * @param  \Modules\MailClient\Models\EmailAccountFolder  $folder
     * @param  \Modules\MailClient\Client\Gmail\Message|null  $message
     */
    protected function setFolderHistoryIdFromMessage($folder, $message): void
    {
        if (is_null($message)) {
            return;
        }

        $folder->setMeta(static::HISTORY_META_KEY, $message->getHistoryId());
    }

    /**
     * Exclude the mailables which are sent from the system notifications
     *
     * @param  \Illuminate\Support\Collection  $messages
     * @return \Illuminate\Support\Collection
     */
    protected function excludeSystemMailables($messages)
    {
        return $messages->filter(
            fn ($message) => is_null($message->getHeader('x-concord-mailable'))
        )->values();
    }

    /**
     * Start account folders synchronization
     */
    public function syncFolders(): void
    {
        if ($this->isAccountRateLimitExceeded()) {
            return;
        }

        parent::syncFolders();
    }

    /**
     * Delete all messages which are queued for delete
     */
    protected function deleteQueuedMessages(): void
    {
        if ($this->isAccountRateLimitExceeded()) {
            return;
        }

        parent::deleteQueuedMessages();
    }

    /**
     * Callback for finisnhed synchronization (may finish with errors)
     */
    protected function finished(): void
    {
        if (! $this->isAccountRateLimitExceeded()) {
            $this->removeAccountSyncAfterFlag();
        }
    }

    /**
     * Check whether account rate limit quota exceeded
     */
    protected function isAccountRateLimitExceeded(): bool
    {
        $continueAfter = $this->account->getMeta('_continue_sync_after');

        // We will add 15 minutes to allow Google to properly clear all quota limits
        // While testing we've discovered that if sync is retried 15 minutes after the retry after
        // timestamp, most likely won't fail again
        return ! empty($continueAfter) && Carbon::parse($continueAfter)->addMinutes(15)->isFuture();
    }

    /**
     * Set the account sync flag from the given exception
     *
     * @param  \Google\Service\Exception  $exception
     * @return string
     */
    protected function setAccountSyncAfterFlag($exception)
    {
        $retryAfter = Str::after($exception->getErrors()['message'], 'Retry after ');
        $this->account->setMeta('_continue_sync_after', $retryAfter);

        return $retryAfter;
    }

    /**
     * Remove the account sync flag
     */
    protected function removeAccountSyncAfterFlag(): void
    {
        $this->account->removeMeta('_continue_sync_after');
    }

    /**
     * Check whether the given exception is rate limit exceeded
     *
     * @param  \Google\Service\Exception  $exception
     */
    protected function isRateLimitExceededException($exception): bool
    {
        return $exception->getCode() == 403 && $exception->getErrors()['reason'] == 'rateLimitExceeded';
    }
}
