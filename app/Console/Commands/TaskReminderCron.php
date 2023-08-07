<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TaskReminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remind-task-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminders = \App\Models\TaskReminder::where('remind_at', '<=', now())
            ->where('reminded_at', null)->with(['recipient', 'task', 'sender'])
            ->get();

        if($reminders->count() > 0) {
            foreach($reminders as $reminder) {
                $this->LogInfo('Sending reminder to ' . $reminder->recipient->name);
                $reminder->recipient->notify(new \App\Notifications\Admin\TaskReminder($reminder));
                $reminder->reminded_at = now();
                $reminder->save();
            }
        } else {
            $this->LogInfo('No reminders to send');
        }
    }

    public function LogInfo($message)
    {
        Log::Info($message);
    }
}
