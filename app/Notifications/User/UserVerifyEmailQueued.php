<?php
namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Notifications\VerifyEmail;

class UserVerifyEmailQueued extends VerifyEmail implements ShouldQueue
{
    use Queueable;

    // Nothing else needs to go here unless you want to customize
    // the notification in any way.
}
