<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EmailDelivered
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

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
      $model = 'App\\Models\\' . $event->sent_email->getHeader('X-Model');
      $model_id = $event->sent_email->getHeader('X-ID');
      if ($model_id) {
        $instance = $model::find($model_id);
        $instance->createLog('Email Delivered');
        $instance->update(['status' => 'delivered']);
      }
    }
}
