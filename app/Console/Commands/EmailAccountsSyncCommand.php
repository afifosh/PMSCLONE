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

namespace App\Console\Commands;

use App\Innoclapps\Date\Carbon;
use Illuminate\Console\Command;
use App\Events\EmailAccountsSyncFinished;
use App\Contracts\Repositories\EmailAccountRepository;
use App\MailClient\EmailAccountSynchronizationManager;
use App\MailClient\Exceptions\SynchronizationInProgressException;


class EmailAccountsSyncCommand extends Command
{
    /**
     * The lock key for the settings
     *
     * @var string
     */
    const LOCK_KEY = 'email-accounts-sync-in-progress';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'concord:sync-email-accounts
                        {--account= : Email account ID}
                        {--broadcast : Whether to broadcast events}
                        {--manual : Whether the sync is invoked manually via the UI}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronizes email accounts.';

    /**
     * Execute the console command.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     *
     * @throws App\MailClient\Exceptions\SynchronizationInProgressException
     *
     * @return void
     */
    public function handle(EmailAccountRepository $repository)
    {
        $this->checkIfAlreadyRunning();

        \Log::info('Gathering email accounts to sync.');

        $accounts = $this->getAccounts($repository);

        if ($accounts->isEmpty()) {
            \Log::error('No accounts found for synchronization.');
        }

        $this->sync($accounts, $repository);
    }

    /**
     * Sync the email accounts
     *
     * @param \Illuminate\Support\Collection $accounts
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     *
     * @return void
     */
    protected function sync($accounts, $repository)
    {
        $synced = false;
        // When the "inital sync from" option "now" is selected and the sync runs for first time
        // and if nothing is synchronized the UI message that initial sync is not performed won't be removed
        // In this case, will make sure to broadcast so the accounts are refetched
        $hasInitialSync = false;

        foreach ($accounts as $account) {
            if (! $account->isInitialSyncPerformed()) {
                $hasInitialSync = true;
            }

            \Log::info(sprintf('Starting synchronization for account %s.', $account->email));

            if (EmailAccountSynchronizationManager::getSynchronizer($account)->setCommand($this)->perform()) {
                $synced = true;
            }

            $repository->update(['last_sync_at' => Carbon::now()], $account->id);
        }

        if ($this->option('broadcast')) {
            event(new EmailAccountsSyncFinished($synced || $hasInitialSync));
        }
    }

    /**
     * Get the accounts that should be synced
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getAccounts($repository)
    {
        $accounts = $repository->with(['oAuthAccount', 'folders'])->getSyncable();
        \Log::info('accounts'.json_encode($accounts));
        if ($this->option('account')) {
            $accounts = $accounts->filter(function ($account) {
                return $account->id === (int) $this->option('account');
            })->values();
        }

        return $accounts;
    }

    /**
     * Remove lock
     */
    public static function removeLock() : void
    {
        settings()->forget(static::LOCK_KEY)->save();
    }

    /**
     * Set lock
     */
    public static function setLock() : void
    {
        settings([static::LOCK_KEY => true]);
    }

    /**
     * Checks whether the command is already running
     *
     * E.q. when trying to sync the mailbox manually via the front-end
     * can happen this command to already runs in background
     *
     * @return void
     *
     * @throws \App\MailClient\Exceptions\SynchronizationInProgressException
     */
    protected function checkIfAlreadyRunning() : void
    {
        if (! $this->option('manual')) {
            return;
        }

        throw_if(
            ! is_null(settings(static::LOCK_KEY)),
            SynchronizationInProgressException::class
        );
    }
}
