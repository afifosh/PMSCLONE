<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Illuminate\Support\MessageBag;
use App\Events\TwoFactorCodeEvent;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
Use \Carbon\Carbon;

class RedirectToDeviceAuthorization extends RedirectIfTwoFactorAuthenticatable
{
    protected $verify;
    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @param  \Laravel\Fortify\LoginRateLimiter  $limiter
     * @return void
     */
    public function __construct(StatefulGuard $guard, LoginRateLimiter $limiter)
    {
        $this->verify = "afifosh-verify";
        parent::__construct($guard, $limiter);
    }



//     public function VerifyCode(Request $request)
//     {   
  
//     //   $request->validate([
//     //     'code' => 'required',
//     // ]);
//     $message = __('The provided two factor authentication code was invalid.');

//     if ($request->wantsJson()) {
//         throw ValidationException::withMessages([
//             'code' => [$message],
//         ]);
//     }
//     return redirect()->back()->withErrors(['current_password' => __('Current password is not correct')]);
//     return redirect()->route('two-factor.login')->withErrors(['code' => $message]);
//     // {{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])}}
//    // return $this->sendRes('File Shared Successfully', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
//    // return $this->sendRes('Email OTP Successfully Enabled', ['event' => 'page_reload', 'close' => 'modal']);
//    $user = $request->challengedUser();  
  
//         if ($user->two_factor_code != $request->code || Carbon::parse($user->two_factor_expires_at)->lt(Carbon::now())) {
//           return app(FailedTwoFactorLoginResponse::class);
//         }
       
//         $user->forceFill([
//           'two_factor_email_confirmed' => now(),
//         ])->save();
//         return $this->sendRes('Email OTP Successfully Enabled');
//    }

    public function verify(TwoFactorLoginRequest $request)
    {

        $user = $request->challengedUser();  

        $request->validate([
            'code' => 'required|numeric|digits:6',
          ],[
            'code.required' => 'code is required',
            'code.numeric' => 'code must be numeric from 0 to 9',
        ]);

        if ($user->two_factor_code != $request->code || Carbon::parse($user->two_factor_expires_at)->lt(Carbon::now())) {
            return app(FailedTwoFactorLoginResponse::class);
        }
        // if ($code = $request->validRecoveryCode()) {
        //     $user->replaceRecoveryCode($code);
        // } elseif (! $request->hasValidCode()) {
        //     return app(FailedTwoFactorLoginResponse::class);
        // }

        // $this->guard->login($user, $request->remember());

        // $request->session()->regenerate();

        // return app(TwoFactorLoginResponse::class);

        // $user = $this->validateCredentials($request);
        // dd($request);
        // $request->validate([
        //     'code' => 'required',
        // ]);


        // dd($user);
        // if ($request->code == $user->two_factor_code) {

        // }

        // if (optional($user)->two_factor_secret &&
        //     in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
        //     return $this->twoFactorChallengeResponse($request, $user);
        // }

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_secret &&
                ! is_null(optional($user)->two_factor_confirmed_at) &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {

                    $request->session()->put([
                        'login.id' => $user->getKey(),
                        'login.remember' => $request->filled('remember'),
                        'login.authenticate_via' => 'google_authenticator',
                    ]);

                    if($request->is('admin/*')){
                        return $request->wantsJson()
                        ? response()->json(['two_factor' => true,
                        'authenticate_via' => "google_authenticator" ])
                        : redirect()->route('admin.two-factor.login');
                    }
            
                    return $request->wantsJson()
                                ? response()->json(['two_factor' => true,
                                'authenticate_via' => "google_authenticator" ])
                                : redirect()->route('two-factor.login');
            } else {
                    $this->guard->login($user, $request->remember());

                    $request->session()->regenerate();

                    return app(TwoFactorLoginResponse::class);
            }
        }             


              
        // $this->guard->login($user, $request->remember());

        // $request->session()->regenerate();

        //return app(TwoFactorLoginResponse::class);
        return app(RedirectIfTwoFactorAuthenticatable::class);

        // if($request->is('admin/*')){
        //     return $request->wantsJson()
        //     ? response()->json(['two_factor' => false,
        //     'authenticate_via' => "google_authenticator" ])
        //     : redirect()->route('admin.two-factor.login');
        // }   
             
       // return redirect()->back()->withStatus('Phone number verified successfully.');
    }   

    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);


        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                // Send otp to user from here
                //   dd($user,$guard);
                $user->generateTwoFactorCode();
                event(new TwoFactorCodeEvent($user));
                return $this->twoFactorChallengeResponse($request, $user);
            } else {
                return $next($request);
            }
        }

        // if (Fortify::confirmsTwoFactorAuthentication()) {
        //     if (in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
        //         // Send otp to user from here
        //      //   dd($user,$guard);
        //         $user->generateTwoFactorCode();
        //         event(new TwoFactorCodeEvent($user));

        //         return $this->twoFactorChallengeResponse($request, $user);
        //     } else {
        //         return $next($request);
        //     }
        // }

        // if (optional($user)->two_factor_secret &&
        //     in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
        //     return $this->twoFactorChallengeResponse($request, $user);
        // }

        // return $next($request);
    }    
    /**
     * Get the two factor authentication enabled response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function twoFactorChallengeResponse($request, $user)
    {
        $request->session()->put([
            'login.id' => $user->getKey(),
            'login.remember' => $request->filled('remember'),
            'login.authenticate_via' => "DeviceAuthorization",
        ]);

        if($request->is('admin/*')){
            return $request->wantsJson()
            ? response()->json(['two_factor' => true,
            'authenticate_via' => "DeviceAuthorization" ])
            : redirect()->route('admin.two-factor.login')->with('success', "Code sent to {$user->email}");
        }

        return $request->wantsJson()
                    ? response()->json(['two_factor' => true,
                    'authenticate_via' => "DeviceAuthorization" ])
                    : redirect()->route('two-factor.login')->with('success', "Code sent to {$user->email}");
    }
}