<?php

namespace App\Console;

use App\Console\Commands\ContractExpiryNotificationCron;
use App\Console\Commands\TaskReminderCron;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:flush')->weekly();

        $schedule->command(TaskReminderCron::class)->everyMinute()->runInBackground();

        $schedule->command(ContractExpiryNotificationCron::class)->daily()->runInBackground();

        // run queue worker every minute
        $schedule->command('queue:work --tries=3 --max-time=300 --stop-when-empty')
          ->everyMinute()
          ->withoutOverlapping()
          ->runInBackground();
    }

    protected function syncMethodFromConfigValue($value)
    {
        return match ($value) {
            'hourly' => 'hourly',
            default  => 'every' . ucfirst($value),
        };
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
