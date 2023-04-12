<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Illuminate\Support\Str;

class CaptchaValidations  extends RedirectIfTwoFactorAuthenticatable
{
    public function __invoke(Request $request, $next)
    {
        // Validator::make($request->all(), [
        //     'captcha' => 'required'
        // ])->validate();

        $user = $request->challengedUser();  
        $this->guard->login($user, $request->remember());

        $request->session()->regenerate();

        // return $this->challengedUser = $user;

        // if ( true ) {
        //     return back()->withErrors( "Errorrrrrrrrrrrrrrrrrrrrr");
        // }
           //   $request['afifosh'] =  "northleb northleb";
        return $next($request);
    }

    public function handle($request, $next)
    {
        $user = $request->challengedUser();  
        if ($request->session()->has('login.fingerprint')) {
                
                $user->deviceAuthorizations()->create([
                    'uuid'         => Str::uuid(),
                    'fingerprint'  => $request->session()->get('login.fingerprint'),
                    'user_agent'   => $request->session()->get('login.browser'),
                    'ip_address'   => $request->session()->get('login.ip'),
                    'verify_token' => Str::random(40),
                    'verified_at'  =>  now(),
                ]);

        }
  


       $user = $request->challengedUser();  
       $this->guard->login($user, $request->remember());

       $request->session()->regenerate();
       return app(TwoFactorLoginResponse::class);
     //  return $next($request);
    }
}