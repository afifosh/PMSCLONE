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

namespace Modules\Activities\Console\Commands;

use Illuminate\Console\Command;
use Modules\Activities\Models\Activity;
use Modules\Activities\Notifications\ActivityReminder;

class ActivitiesNotificationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:due-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies owners of due activities.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Activity::reminderable()->with(['user', 'type'])->get()->each(function ($activity) {
            try {
                $activity->user->notify(new ActivityReminder($activity));
            } finally {
                // To avoid infinite loops in case there are error, we will
                // mark the activity as notified
                $activity->forceFill(['reminded_at' => now()])->save();
            }
        });
    }
}
