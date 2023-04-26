<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\LoginRateLimiter;
Use \Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Jobs\SendLostRecoveryCodeNotification;
class LostRecoveryCodeController extends Controller
{


    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function view(Request $request)
    {
        $guard = $this->getUserGuard();
        return view($guard === 'admin' ? 'admin.auth.lost-recovery-codes' : 'auth.lost-recovery-codes');
    }

    /**
     * Get the two factor authentication recovery codes for authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (! $request->user()->two_factor_secret ||
            ! $request->user()->two_factor_recovery_codes) {
            return [];
        }

        return response()->json(json_decode(decrypt(
            $request->user()->two_factor_recovery_codes
        ), true));
    }


    public function getUserGuard(){

        return config('fortify.guard');

    }    

    /**
     * Generate a fresh set of two factor authentication recovery codes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Laravel\Fortify\Actions\GenerateNewRecoveryCodes  $generate
     * @return \Laravel\Fortify\Contracts\RecoveryCodesGeneratedResponse
     */
    public function sendRecoveryCodes(Request $request)
    {

        $guard = $this->getUserGuard();

        if($guard == "admin"){
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:admins',
            ], [
                'email.exists' => 'We can\'t find a user with that email address.',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'email' => 'required|exists:users',
            ], [
                'email.exists' => 'We can\'t find a user with that email address.',
            ]);
        }

    
        // Check if the validation fails
        if ($validator->fails()) {
            // Handle the validation errors
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $email = $request->input('email');
        
        if($guard == "admin"){
            $user = Admin::where('email',$email)->first();
        }else{
            $user = User::where('email',$email)->first();
        }




    if (optional($user)->two_factor_secret &&
        ! is_null(optional($user)->two_factor_confirmed_at) &&
        in_array(TwoFactorAuthenticatable::class, class_uses_recursive($user))) {    
            dispatch(new SendLostRecoveryCodeNotification($user));
            return redirect()->back()->with('status', 'We have emailed your recovery codes!');
        }else{
           return redirect()->back()->withErrors([
            'email' => 'You are not allowed to perform this action.'
        ]);
        }

       // $user->generateTwoFactorCode();
      //  event(new TwoFactorCodeEvent($user));
    //   dd($user);

    }    



    
}
