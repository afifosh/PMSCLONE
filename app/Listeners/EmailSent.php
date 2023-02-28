<?php

namespace App\Listeners;

use App\Models\CompanyInvitation;
use App\Models\Program;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use jdavidbakr\MailTracker\Events\EmailSentEvent;

class EmailSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle(EmailSentEvent $event)
    {
      $p = Program::first();
      $p->update(['description' => json_encode($event)]);
      $tracker = $event->sent_email;
      $model = 'App\\Models\\'.$event->sent_email->getHeader('X-Model');
      $model_id = $event->sent_email->getHeader('X-ID');
      $instance = $model::find($model_id);
      $instance->update(['token' => 'email_sent']);
      // Perform your tracking/linking tasks on $model knowing the SentEmail object
    }
}
