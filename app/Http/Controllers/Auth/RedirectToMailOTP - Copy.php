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
use Illuminate\Routing\Pipeline;
use Illuminate\Support\Str;
use App\Models\DeviceAuthorization;
use Illuminate\Database\Eloquent\Model;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use App\Actions\Fortify\CaptchaValidations;
class RedirectToMailOTP extends RedirectIfTwoFactorAuthenticatable
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

        // $request->session()->put([
        //     'login.id' => $user->getKey(),
        //     'login.remember' => $request->filled('remember'),
        //     'login.authenticate_via' => 'google_authenticator',
        // ]);

        $request->session()->forget('login.authenticate_via');
        return  app(\Illuminate\Pipeline\Pipeline::class)->send($request)
        ->through([
            RedirectIfTwoFactorAuthenticatable::class,
           CaptchaValidations::class,
            // AttemptToAuthenticate::class,
        
            // PrepareAuthenticatedSession::class,
        //   RedirectIfTwoFactorAuthenticatable::class,
            
        ])->thenReturn();

        return app(RedirectIfTwoFactorAuthenticatable::class)->handle($request,AttemptToAuthenticate::class);

dd("afifosh");
dd($request);
        return (new Pipeline(app()))->send($request)->through(
            RedirectIfTwoFactorAuthenticatable::class,
         );

        return app(TwoFactorLoginResponse::class);
  
        return (new Pipeline(app()))->send($request)->through(
            AttemptToAuthenticate::class,
           PrepareAuthenticatedSession::class,
         );

                $result = app(\Illuminate\Pipeline\Pipeline::class)
        ->send($request)
        ->through(
            AttemptToAuthenticate::class,
           PrepareAuthenticatedSession::class,
         )->thenReturn();
   dd(  $result);

        return (new Pipeline(app()))->send($request)->through(array_filter([
            // config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            // Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));

        // return $this->loginPipeline($request)->then(function ($request) {
        //     return app(LoginResponse::class);
        // });     
        // return (new Pipeline(app()))->send($request)->through(array_filter([
        //     RedirectIfTwoFactorAuthenticatable::class,
        //     AttemptToAuthenticate::class,
        //     PrepareAuthenticatedSession::class,
        // ]));

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

    }   

    public function verifysss(TwoFactorLoginRequest $request)
    {  
        // dd(session()->all());
        //       dd($request);
        $request->session()->forget('login.authenticate_via');
        return  app(\Illuminate\Pipeline\Pipeline::class)->send($request)
        ->through([
           CaptchaValidations::class,
            // AttemptToAuthenticate::class,
        
            // PrepareAuthenticatedSession::class,
        //   RedirectIfTwoFactorAuthenticatable::class,
            
        ])->thenReturn();
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

        $ip                = $request->ip();
        $userAgent         = $request->userAgent();
        $known = $user->authentications()->whereIpAddress($ip)->whereUserAgent($userAgent)->whereLoginSuccessful(true)->first();
        $newUser = Carbon::parse($user->{$user->getCreatedAtColumn()})->diffInMinutes(Carbon::now()) < 1;

        //   dd($userAgent);
        // Create the authorizations
        $user->deviceAuthorizations()->create([
            'uuid'         => Str::uuid(),
            'fingerprint'  => Str::random(128),
            'user_agent'      => $userAgent,
            'ip_address'           => $ip,
            'verify_token' => Str::random(40),
            'verified_at'  =>  now(),
        ]);
   // dd($user);

        if (Fortify::confirmsTwoFactorAuthentication()) {
            if (optional($user)->two_factor_email_confirmed &&
                 ! is_null(optional($user)->two_factor_email_confirmed_at) && 
                //  ! $known && 
                //  ! $newUser &&
                //   config('authentication-log.notifications.new-device.enabled') &&
                in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
                // Send otp to user from here
            //    dd($user);
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
            'login.authenticate_via' => "email",
        ]);

        if($request->is('admin/*')){
            return $request->wantsJson()
            ? response()->json(['two_factor' => true,
            'authenticate_via' => "email" ])
            : redirect()->route('admin.two-factor.login')->with('success', "Code sent to {$user->email}");
        }

        return $request->wantsJson()
                    ? response()->json(['two_factor' => true,
                    'authenticate_via' => "email" ])
                    : redirect()->route('two-factor.login')->with('success', "Code sent to {$user->email}");
    }
}