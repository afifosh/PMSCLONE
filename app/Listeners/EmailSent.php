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
    $model = 'App\\Models\\' . $event->sent_email->getHeader('X-Model');
    $model_id = $event->sent_email->getHeader('X-ID');
    if ($model_id) {
      $instance = $model::find($model_id);
      $instance->createLog('Email Sent');
      $instance->update(['status' => 'sent']);
    }
  }
}
