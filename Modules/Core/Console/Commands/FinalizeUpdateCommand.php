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

use Illuminate\Console\Command;
use Modules\Core\Updater\UpdateFinalizer;

class FinalizeUpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updater:finalize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Finalize the application recent update.';

    /**
     * Execute the console command.
     */
    public function handle(UpdateFinalizer $instance): void
    {
        if (! $instance->needed()) {
            $this->info('There is no update to finalize');

            return;
        }

        $instance->run();

        $this->info('The update has been finalized');
    }
}
