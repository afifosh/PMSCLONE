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

namespace App\Innoclapps\Updater;

use App\Innoclapps\Application;
use Illuminate\Support\Facades\Artisan;

class UpdateFinalizer
{
    /**
    * Check whether finalization of the update is needed
    *
    * @return boolean
    */
    public function needed() : bool
    {
        return version_compare($this->getCachedCurrentVersion(), Application::VERSION, '<');
    }

    /**
    * Run the update finalizer
    *
    * @return boolean
    */
    public function run() : bool
    {
        if (! $this->needed()) {
            return false;
        }

        $this->runFinalizeCommands();
        $this->runPatchers();

        settings([
            '_version'           => Application::VERSION,
            '_last_updated_date' => date('Y-m-d H:i:s'),
            '_updated_from'      => $this->getCachedCurrentVersion(),
        ]);

        $this->runOptimizer();
        $this->restartQueue();

        return true;
    }

    /**
    * Get the cached current version
    *
    * @return string
    */
    public function getCachedCurrentVersion() : string
    {
        return settings('_version') ?: '1.0.7';
    }

    /**
    * Optimize the application
    *
    * @return void
    */
    protected function runOptimizer() : void
    {
        $command = config('updater.optimize');

        if ($command && ! app()->runningUnitTests() && app()->isProduction()) {
            $this->executeCommands([$command]);
        }
    }

    /**
    * Restarthe queue
    *
    * @return void
    */
    protected function restartQueue() : void
    {
        if (config('updater.restart_queue')) {
            // Restart the queue (if configured)
            try {
                Artisan::call('queue:restart');
            } catch (\Exception $e) {
            }
        }
    }

    /**
    * Run the finalizer commands
    *
    * @return void
    */
    protected function runFinalizeCommands() : void
    {
        $this->executeCommands(config('updater.commands.finalize'));
    }

    /**
    * Execute the updates patchers
    *
    * @return void
    */
    protected function runPatchers() : void
    {
        app(Migration::class)->patchers()
            // Get all the versions starting from current cached (excluding current cached as this one is already executed)
            // between the latest available update for the current version (including current)
            ->filter(
                fn ($patch) => ! (version_compare($patch->version(), $this->getCachedCurrentVersion(), '<=') ||
                    version_compare($patch->version(), Application::VERSION, '>'))
            )
            ->filter->shouldRun()
            ->each->run();
    }

    /**
     * Run the given finalizer commands
     *
     * @param array $commands
     *
     * @return void
     */
    protected function executeCommands(array $commands) : void
    {
        foreach ($commands as $command) {
            $name   = is_array($command) ? $command['class'] : $command;
            $params = is_array($command) ? $command['params'] ?? [] : [];

            Artisan::call($name, $params);
        }
    }
}
