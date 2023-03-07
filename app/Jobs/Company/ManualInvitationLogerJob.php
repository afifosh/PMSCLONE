<?php

namespace App\Jobs\Company;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ManualInvitationLogerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $invitation;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $invitation = $this->invitation;
      if($invitation->status == 'sent'){
        $invitation->update(['status' => 'clicked']);
        $invitation->createLog('Email Seen');
        $invitation->createLog('Email Link Clicked');
      }
    }
}
