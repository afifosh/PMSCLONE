<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuccessfulLoginListener
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
        if (Auth::check()) {
            $request = app(Request::class);
            // $fingerprint = $request->fingerprint;
            // $ip                = $request->ip();
            // $userAgent         = $request->userAgent();
    
            $user = $event->user;
            
            
            $user->addDeviceAuthorization([
                'uuid'         => Str::uuid(),
                'fingerprint'  => $request->session()->get('login.fingerprint'),
                'user_agent'   => $request->session()->get('login.browser'),
                'ip_address'   => $request->session()->get('login.ip'),
                'token' => "afifosh",
            ]);
            // Log the successful login
            Log::info('Userssssssssssss ' . $event->user->email . ' has successfully logged in.');
    
            // Your additional code here
        }
    }
}