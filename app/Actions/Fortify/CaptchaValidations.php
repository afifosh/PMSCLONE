<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Illuminate\Support\Str;
use App\Rules\FingerprintValidationRule;

class CaptchaValidations  extends RedirectIfTwoFactorAuthenticatable
{
    public function __invoke(Request $request, $next)
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

        return $next($request);
        
        // The validation passed, continue with the rest of the logic

        // return $next($request);

        // Validator::make($request->all(), 
        //     [ 
        //         'g-recaptcha-response' => 'required|captcha' 
        //     ],
        //     [
        //         'required' => 'Captcha verification faild, try again!',
        //         'captcha'  => 'Captcha verification faild, try again!'
        //     ]
        // )->validate();

        // return $next($request);

        // return $this->challengedUser = $user;

        // if ( true ) {
        //     return back()->withErrors( "Errorrrrrrrrrrrrrrrrrrrrr");
        // }
           //   $request['afifosh'] =  "northleb northleb";
    
    }

    public function handle($request, $next)
    {
        $user = method_exists($request, 'challengedUser') ? $request->challengedUser() : $this->validateCredentials($request);  
     //   $user = $request->challengedUser() ? $request->challengedUser() : $this->validateCredentials($request);  
        if ($request->session()->has('login.fingerprint')) {
                
                $validate = $user->addDeviceAuthorization([
                    'uuid'         => Str::uuid(),
                    'fingerprint'  => $request->session()->get('login.fingerprint'),
                    'user_agent'   => $request->session()->get('login.browser'),
                    'ip_address'   => $request->session()->get('login.ip'),
                    'verify_token' => Str::random(40),
                    'verified_at'  =>  now(),
                ]);
               // dd($validate);

    //   dd($request);
                if($validate){
                    // $user = $request->challengedUser();  
                  //  $this->guard->login($user, $request->remember());
                    $this->guard->login($user);
                    $request->session()->regenerate();
                    return app(TwoFactorLoginResponse::class);
                }else{
                    $deviceAuthorization = $user->deviceAuthorizations()->create([
                        'uuid'         => Str::uuid(),
                        'fingerprint'  => $request->session()->get('login.fingerprint'),
                        'user_agent'   => $request->session()->get('login.browser'),
                        'ip_address'   => $request->session()->get('login.ip'),
                        'verify_token' => Str::random(40),
                        'verified_at'  =>  now(),
                    ]);     
                    $deviceAuthorization->safe = true;
                    $this->guard->login($user);
                    $request->session()->regenerate();
                    return app(TwoFactorLoginResponse::class);
                }
        }
  
       // return $next($request);

    //    $user = $request->challengedUser();  
    //    $this->guard->login($user, $request->remember());

    //    $request->session()->regenerate();
    //    return app(TwoFactorLoginResponse::class);
     //  return $next($request);
    }
}