<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\EmailAccountsSyncCommand;

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
        $schedule->call(function () {
            settings()->set('last_cron_run', now());
        })->after(function () {
            settings()->save();
        })->everyMinute();

        // $schedule->command('inspire')->hourly();
        $schedule->command('queue:work --tries=3 --max-time=300 --stop-when-empty')
          ->everyMinute()
          ->withoutOverlapping();

          $schedule->command(EmailAccountsSyncCommand::class, ['--broadcast'])
          ->{$this->syncMethodFromConfigValue(config('app.mail_client.sync.every'))}()
          ->withoutOverlapping(30)
          ->before(fn () => EmailAccountsSyncCommand::setLock())
          ->after(fn ()  => EmailAccountsSyncCommand::removeLock())
          ->sendOutputTo(storage_path('logs/email-accounts-sync.log'));

        $schedule->command('queue:flush')->weekly();
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
