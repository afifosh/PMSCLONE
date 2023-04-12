<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
Use \Carbon\Carbon;

class CheckDeviceAuthorization extends RedirectIfTwoFactorAuthenticatable
{
    public function __invoke(Request $request, $next)
    {

    }

    public function handle($request, $next)
    {


        $user = $this->validateCredentials($request);
        $ip                = $request->ip();
        $userAgent         = $request->userAgent();
        $fingerPrint       = $request->fingerprint;

        $request->session()->put([
            'login.ip' => $ip,
            'login.browser' => $userAgent,
            'login.fingerprint' => $fingerPrint,
        ]);
            
        $known = $user->deviceAuthorizations()->whereIpAddress($ip)
        ->whereUserAgent($userAgent)
        ->whereFingerprint($fingerPrint)
        ->where( 'updated_at', '>', Carbon::now()->subDays(30))->first();


        if($request->has('fingerprint')   &&  $known  ) {

            return app(\Illuminate\Pipeline\Pipeline::class)->send($request)
            ->through([
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ])->then(function ($request) use ($next) {
                return $next($request);
        });

        } else {

            return $next($request);

        }


    }
}