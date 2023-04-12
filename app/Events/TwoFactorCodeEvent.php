<?php

namespace App\Events;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

}