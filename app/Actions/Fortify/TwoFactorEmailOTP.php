<?php

namespace App\Actions\Fortify;

use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
use Laravel\Fortify\TwoFactorAuthenticatable;
Use \Carbon\Carbon;

use Illuminate\Http\Request;

class TwoFactorEmailOTP extends RedirectIfTwoFactorAuthenticatable
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
        parent::__construct($guard, $limiter);
    }
 
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        $user = $this->validateCredentials($request);

        if (optional($user)->two_factor_secret &&
            in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {
            return $this->twoFactorChallengeResponse($request, $user);
        }
      //  dd($request);
        return $next($request);
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
        //dd(session()->all());
      //  dd($request);
        //$request['next'] = $next;
       // return $next($request);
       // dd($next);
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