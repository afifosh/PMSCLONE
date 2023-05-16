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

namespace Modules\Core\Console\Commands;

use App\Installer\RequirementsChecker;
use Illuminate\Console\Command;
use Modules\Core\Updater\Updater;
use Throwable;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updater:update {--key= : Purchase key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the application to the latest available version.';

    /**
     *  Execute the console command.
     */
    public function handle(RequirementsChecker $requirements, Updater $updater)
    {
        if ($requirements->fails('zip')) {
            $this->error(__('core::update.update_zip_is_required'));

            return 1;
        }

        $this->info('Configuring purchase key');
        $updater->usePurchaseKey($this->option('key') ?: '');

        if (! $updater->isNewVersionAvailable()) {
            $this->info('The latest version '.$updater->getVersionInstalled().' is already installed.');

            return;
        }

        $this->warn('Updating... this may take several minutes');

        $this->down();

        try {
            if (! $this->getLaravel()->runningUnitTests()) {
                $this->info('Increasing PHP config values');

                $this->increasePhpIniValues();
            }

            $this->info('Performing update');
            $updater->update($updater->getVersionAvailable());
        } catch (Throwable $e) {
            $this->up();

            throw $e;
        } finally {
            $this->up();
        }
    }

    /**
     * Bring the application out of maintenance mode
     */
    protected function up(): void
    {
        $this->info('Bringing the application out of maintenance mode');
        $this->call('up');
    }

    /**
     * Put the application into maintenance mode
     */
    protected function down(): void
    {
        $this->info('Putting the application into maintenance mode');
        $this->call('down', ['--render' => 'core::errors.updating']);
    }

    /**
     * Increase php.ini values if possible
     */
    protected function increasePhpIniValues(): void
    {
        \DetachedHelper::raiseMemoryLimit('256M');
        @ini_set('max_execution_time', 240);
    }
}
