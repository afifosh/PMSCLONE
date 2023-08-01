<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Synchronization;

use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Synchronization\Exceptions\SyncFolderTimeoutException;

class EmailAccountSynchronizationManager
{
    /**
     * Time limit to sync account in seconds
     */
    const MAX_ACCOUNT_SYNC_TIME = 240;

    /**
     * Max time in seconds between saved DB batches
     */
    const DB_BATCH_TIME = 60;

    /**
     * Force mode indicator
     */
    const FORCE_MODE = 'force';

    /**
     * Chill mode indicator
     */
    const CHILL_MODE = 'chill';

    /**
     * Number of seconds passed to store last emails batch
     *
     * @var int
     */
    protected $batchSaveTime = -1;

    /**
     * Timestamp when last batch was saved
     *
     * @var int
     */
    protected $batchSaveTimestamp = 0;

    /**
     * Mode for the sync process
     *
     * @var string chill|force
     */
    protected $mode = self::CHILL_MODE;

    /**
     * @var \App\Console\Commands\EmailAccountsSynchronization|null
     */
    protected $command;

    /**
     * Get the synchronizer class
     *
     *
     * @return mixed
     */
    public static function getSynchronizer(EmailAccount $account)
    {
        $part = $account->connection_type->value;

        return self::{'get'.$part.'Synchronizer'}($account);
    }

    /**
     * Get the IMAP account synchronizer
     *
     *
     * @return ImapEmailAccountSynchronization
     */
    public static function getImapSynchronizer(EmailAccount $account)
    {
        return resolve(ImapEmailAccountSynchronization::class, ['account' => $account]);
    }

    /**
     * Get the Gmail account synchronizer
     *
     *
     * @return ImapEmailAccountSynchronization
     */
    public static function getGmailSynchronizer(EmailAccount $account)
    {
        return resolve(GmailEmailAccountSynchronization::class, ['account' => $account]);
    }

    /**
     * Get the Outlook account synchronizer
     *
     *
     * @return ImapEmailAccountSynchronization
     */
    public static function getOutlookSynchronizer(EmailAccount $account)
    {
        return resolve(OutlookEmailAccountSynchronization::class, ['account' => $account]);
    }

    /**
     * Set the command class
     *
     * @param  \App\Console\Commands\EmailAccountsSynchronization  $command
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Check whether the synchronization is in force mode
     *
     * @return bool
     */
    public function isForceMode()
    {
        return $this->mode === self::FORCE_MODE;
    }

    /**
     * Check whether the synchronization is in chill mode
     *
     * @return bool
     */
    public function isChillMode()
    {
        return $this->mode === self::CHILL_MODE;
    }

    /**
     * Check if sync timeout for the current account
     *
     * Excluded in force mode
     *
     * @return bool
     */
    protected function isTimeout()
    {
        if ($this->isForceMode()) {
            return false;
        }

        return time() - $this->processStartTime > self::MAX_ACCOUNT_SYNC_TIME;
    }

    /**
     * Log info if process invoked via command
     *
     * @param  string  $message
     * @return void
     */
    protected function info($message)
    {
        if (! $this->command) {
            return;
        }

        $this->command->info($message);
    }

    /**
     * Log error if process invoked via command
     *
     * @param  string  $message
     * @return void
     */
    protected function error($message)
    {
        if (! $this->command) {
            return;
        }

        $this->command->error($message);
    }

    /**
     * Clean up after folder sync complete
     *
     * @param  null|\Modules\MailClient\Models\EmailAccountFolder  $folder
     * @return void
     */
    protected function cleanUpAfterFolderSyncComplete($folder = null)
    {
        $this->cleanUp(true, $folder);
    }

    /**
     * Tracks time when last batch was saved.
     *
     * Calculates time between batch saves.
     *
     * @param  bool  $isFolderSyncComplete
     * @param  null|\Modules\MailClient\Models\EmailAccountFolder  $folder
     */
    protected function cleanUp($isFolderSyncComplete = false, $folder = null)
    {
        /**
         * In case folder sync completed and batch save time exceeded limit - throws exception.
         */
        if ($isFolderSyncComplete
            && $folder != null
            && $this->isChillMode()
            && $this->batchSaveTime > 0
            && $this->batchSaveTime > static::DB_BATCH_TIME
        ) {
            throw new SyncFolderTimeoutException($folder->account->email, $folder->name);
        } elseif ($isFolderSyncComplete) {
            /**
             * In case folder sync completed without batch save time exceed - reset batchSaveTime.
             */
            $this->batchSaveTime = -1;
        } else {
            /**
             * After batch save - calculate time difference between batches
             */
            if ($this->batchSaveTimestamp !== 0) {
                $this->batchSaveTime = time() - $this->batchSaveTimestamp;

                $this->info(sprintf('Batch save time: "%d" seconds.', $this->batchSaveTime));
            }
        }

        $this->batchSaveTimestamp = time();
    }
}
