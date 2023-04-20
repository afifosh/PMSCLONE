<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use App\Actions\Fortify\CaptchaValidations;
Use \Carbon\Carbon;
use App\Rules\FingerprintValidationRule;

class CheckDeviceAuthorization extends RedirectIfTwoFactorAuthenticatable
{


    public function handle($request, $next)
    {

        // Define the custom validation rule
        $rule = [
            'fingerprint' => ['required', new FingerprintValidationRule],
        ];

        // Define the custom error messages
        $messages = [
            'fingerprint.required' => 'The fingerprint field is required.',
            'fingerprint.fingerprint_validation_rule' => 'The fingerprint is not valid.',
        ];

        // Create a new validator instance
        $validator = Validator::make($request->all(), $rule, $messages);

        // Check if the validation fails
        if ($validator->fails()) {
            // Handle the validation errors
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = $this->validateCredentials($request);
        $ip                = $request->ip();
        $userAgent         = $request->userAgent();
        $fingerPrint       = $request->fingerprint;

        $request->session()->put([
            'login.ip' => $ip,
            'login.browser' => $userAgent,
            'login.fingerprint' => $fingerPrint,
        ]);
            
        // $known = $user->deviceAuthorizations()->whereIpAddress($ip)
        // ->whereUserAgent($userAgent)
        // ->whereFingerprint($fingerPrint)
        // ->where( 'updated_at', '>', Carbon::now()->subDays(30))->first();

        $known = $user->shouldSkipTwoFactor($ip,$userAgent,$fingerPrint);   
        //  dd(  $known );
        if($request->has('fingerprint')   &&  $known  ) {

            return app(\Illuminate\Pipeline\Pipeline::class)->send($request)
            ->through([
                CaptchaValidations::class,
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