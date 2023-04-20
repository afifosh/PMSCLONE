<?php
namespace App\Listeners;

use Illuminate\Auth\Events\Authenticated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AfterAuthenticatedListener
{
    /**
     * Handle the event.
     *
     * @param  Authenticated  $event
     * @return void
     */
    public function handle(Authenticated $event)
    {

        $request = app(Request::class);
        $fingerprint = $request->fingerprint;
        $ip                = $request->ip();
        $userAgent         = $request->userAgent();

        $user = $event->user;
        
        
        $user->addDeviceAuthorization([
            'uuid'         => Str::uuid(),
            'fingerprint'  => $request->session()->get('login.fingerprint'),
            'user_agent'   => $request->session()->get('login.browser'),
            'ip_address'   => $request->session()->get('login.ip'),
            'verify_token' => "afifosh",
            'verified_at'  =>  now(),
        ]);


        // Log the successful login
        Log::info('User ' . $event->user->email . ' has successfully logged in.');

        // Your additional code here
    }
}
