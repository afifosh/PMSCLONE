<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncrementDeviceAuthorizationAttempts
{
    /**
     * Handle the event.
     *
     * @param  Failed  $event
     * @return void
     */
    public function handle(Failed $event)
    {
        if (Auth::check()) {

            $request = app(Request::class);
            $fingerprint = $request->fingerprint;
            $ip                = $request->ip();
            $userAgent         = $request->userAgent();
    
            $user = $event->user;
            
            $deviceAuthorization = $user->deviceAuthorizations()
                ->where('fingerprint', $fingerprint)
                ->whereIpAddress($ip)
                ->whereUserAgent($userAgent)
                ->whereFingerprint($fingerprint)
                ->latest('created_at')
                ->first();
    
            if ($deviceAuthorization) {
                $deviceAuthorization->increment('failed_attempts');
            }
            
        }

    }
}
