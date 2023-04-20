<?php

namespace App\Traits;

use Illuminate\Support\Str;
use App\Events\TwoFactorCodeEvent;
Use \Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use App\Models\Admin;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

trait TwoFactorAuthentication
{

    public function sendEmailOTPVerificationCode($user)
    {
      $throttleKey = 'email-verification:' . strtolower($user->email);
      RateLimiter::hit($throttleKey);
      $user->generateTwoFactorCode();
      event(new TwoFactorCodeEvent($user));
  
    }
  

    public function getUserGuard(){

        return (config('fortify.guard') == "admin") ? "admin" : "user";

    }    

    public function resendCode(Request $request)
    {

        //$guard = request()->is('admin/*') ? 'admin' : 'web';
        $guard = config('fortify.guard');
        $id = Session::get('login.id');
     
        if ($id) {

            if ($guard == 'admin') {
                $user = Admin::find($id);
            } else {
                $user = User::find($id);
            }

        } else {

            if ($guard == 'admin') {
                $user = Auth::guard('admin')->user();
            } else {
                $user = Auth::user();
            }
        }

      $cacheKey = 'otp_sent_at_' . $user->id . '_'.$request->ip();
      $ttl = 60; // 60 seconds
      $expireAt = now()->addMinutes(1);
     if (Cache::add($cacheKey, now(), $expireAt)) {
      $this->sendEmailOTPVerificationCode($user);
      return $this->sendRes('Email OTP Code Sent to  '.$user->email);
     }else{

      $secondsSinceLastOtpSent = $ttl - now()->diffInSeconds(Cache::get($cacheKey));

          $msg = 'Too many requests. Please wait ' .     $secondsSinceLastOtpSent  . ' seconds before trying again.';
          return response()->json(['success' => false, 'message' =>  $msg, 'data'=>$msg], 422);
          return $this->sendError('Something went wrong', ['error' => $msg]);
     }
  

    }
  

    public function VerifyCode(Request $request)
    {   

       
    $user = $request->user();
  
  
    $request->validate([
      'code' => 'required|numeric|digits:6',
    ],[
      'code.required' => 'code is required',
      'code.numeric' => 'code must be numeric from 0 to 9',
    ]);
  
        if ($user->two_factor_code != $request->code || Carbon::parse($user->two_factor_expires_at)->lt(Carbon::now())) {
          $message = __('The provided two factor authentication code was invalid.');
          return response()->json([ 'message' =>  $message, 'errors'=>array('code'=> [$message])], 422);
        }
  
  
       
        $user->forceFill([
          'two_factor_email_confirmed' => 1,
          'two_factor_email_confirmed_at' => now(),
        ])->save();


        return $this->sendRes('Email OTP Successfully Enabled', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
   }
  
  
      /**
       * securitySetting.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return \Illuminate\Contracts\Support\Responsable
       */
      public function securitySetting(Request $request)
      {
      //  $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');
       $user = $request->user();

  
        switch ($request->endpoint) {
            case 'general-info':
                $this->general_info_update($user, $request);
                break;
            case 'update-password':
                $this->update_password($user, $request);
                break;
            case 'two-factor-authentication':
                return $this->two_factor_authentication($user, $request);
                break;
            case 'two-factor-email-authentication':
                return $this->two_factor_email_authentication($user, $request);
                break;              
            case 'browser-sessions':
                $this->logoutOtherBrowserSessions($user, $request);
                break;
            case 'delete-account':
                $this->deleteUser($user);
                break;
            default:
                // Session::flash('error', 'No Tab Found..');
                break;
        }

      }
  
      public  function password_check($user, $password)
      {
          if (!isset($password) || !Hash::check($password, $user->password)) {
              throw ValidationException::withMessages(['password' => 'The provided password does not match your current password.']);
          }
          return true;
      }
  
      public function two_factor_authentication($user, $request)
      {
          if($request->action == 'enable' || $request->action == 'disable'){
            $request->validate([
              'action' => 'required',    
              'password' => ['required', 'string'],
          ]);
          $this->password_check($user, $request->password);
          }

          switch ($request->action) {
              case 'enable':
                 return $this->enableTwoFactorAuthentication($user);
                  break;
              case 'confirm':
                 return $this->confirmTwoFactorAuthentication($user,$request);
                  break;                
              case 'regenerate_code':
                  $this->regenerateRecoveryCodes($user);
                  break;
              case 'show_code':
                  $this->showRecoveryCodes();
                  break;
              case 'disable':
                return $this->disableTwoFactorAuthentication($user);
                  break;
              default:
                  //Session::flash('error', 'Invalid Action');
                  break;
          }
      }
  
  
      public function two_factor_email_authentication($user, $request)
      {
  
          switch ($request->action) {
              case 'enable':
                 return $this->enableEmailTwoFactorAuthentication($user, $request);
                  break;
              case 'disable':
                return $this->disableEmailTwoFactorAuthentication($user, $request);
                  break;
              default:
                  //Session::flash('error', 'Invalid Action');
                  break;
          }
      }
  
  
      /**
       * Confirm two factor authentication for the user.
       *
       * @param  \Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication  $disable
       * @return void
       */
      public function confirmTwoFactorAuthentication($user,$request)
      {
          $twoFaProvider = App::make('Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider');
  
          $confirm = new ConfirmTwoFactorAuthentication($twoFaProvider);
          $confirm($user, $request->input('code'));
          
          return $this->sendRes('Two Factor Authentication Successfully Activated', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
           

      }    
      /**
       * Disable two factor authentication for the user.
       *
       * @param  \Laravel\Fortify\Actions\DisableTwoFactorAuthentication  $disable
       * @return void
       */
      public function disableTwoFactorAuthentication($user)
      {
          $twoFaProvider = App::make('Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider');
  
          $disable = new DisableTwoFactorAuthentication($twoFaProvider);
          $disable($user);
  
          $guard = $this->getUserGuard();
           
          return $this->sendRes('Two Factor Authentication Successfully Disabled', ['event' => 'redirect', 'url' => route($guard.'.'.$guard.'-account.edit', [$guard.'_account' => auth()->id(), 't' => 'security'])]);
           

      }
      /**
       * Enable two factor authentication for the user.
       *
       * @param  \Laravel\Fortify\Actions\EnableTwoFactorAuthentication  $enable
       * @return void
       */
      public function enableTwoFactorAuthentication($user)
      {
          $twoFaProvider = App::make('Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider');
  
          $enable = new EnableTwoFactorAuthentication($twoFaProvider);
          $enable($user);
  
          $guard = $this->getUserGuard();

          return $this->sendRes('Two Factor Authentication Successfully Enabled', ['event' => 'redirect', 'url' => route($guard.'.'.$guard.'-account.edit', [$guard.'_account' => auth()->id(), 't' => 'security'])]);
           
      }  
      
      
      public function enableEmailTwoFactorAuthentication($user,Request $request)
      {   

      $user = $request->user();
    
    
      $request->validate([
        'code' => 'required|numeric|digits:6',
      ],[
        'code.required' => 'code is required',
        'code.numeric' => 'code must be numeric from 0 to 9',
      ]);
    
          if ($user->two_factor_code != $request->code || Carbon::parse($user->two_factor_expires_at)->lt(Carbon::now())) {
            $message = __('The provided two factor authentication code was invalid.');
            return response()->json([ 'message' =>  $message, 'errors'=>array('code'=> [$message])], 422);
          }
    
    
         
          $user->forceFill([
            'two_factor_email_confirmed' => 1,
            'two_factor_email_confirmed_at' => now(),
          ])->save();

          $guard = $this->getUserGuard();
          
          return $this->sendRes('Email OTP Successfully Enabled', ['event' => 'redirect', 'url' => route($guard.'.'.$guard.'-account.edit', [$guard.'_account' => auth()->id(), 't' => 'security'])]);
     }
    
      /**
       * Confirm the user's password.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return \Illuminate\Contracts\Support\Responsable
       */
      public function disableEmailTwoFactorAuthentication($user,Request $request)
      {
     //   $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');
  
  
  
          if (optional($user)->two_factor_email_confirmed &&
            ! is_null(optional($user)->two_factor_email_confirmed_at)) {
  
          
              $confirmed = app(ConfirmPassword::class)(
                Auth::guard() , $request->user(), $request->input('password')
              );
                
             // dd($confirmed);
              if ($confirmed) {
                  $message = __('The provided password is correct.');
                  // $request->session()->put('auth.password_confirmed_at', time());
                       
                  $user->forceFill([
                    'two_factor_email_confirmed' => 0,
                    'two_factor_email_confirmed_at' => null,
                  ])->save();
      
       
              }else{
                  $message = __('The provided password was incorrect.');
              }
  
              
             $guard = $this->getUserGuard();

              return $confirmed
                          ? $this->sendRes('Email OTP Successfully Disabled', ['event' => 'redirect', 'url' => route($guard.'.'.$guard.'-account.edit', [$guard.'_account' => auth()->id(), 't' => 'security'])])
                          : response()->json([ 'message' =>  $message, 'errors'=>array('password'=> [$message])], 422);
  
          } else {
            $message = __('You are not allowed to perform this action.');
             return  response()->json([ 'message' =>  $message, 'errors'=>array('password'=> [$message])], 422);
          }
  
  
        }
  
      /**
       * Confirm the user's password.
       *
       * @param  \Illuminate\Http\Request  $request
       * @return \Illuminate\Contracts\Support\Responsable
       */
      public function twoFactorGoogle($user,Request $request)
      {
       // $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');
  
  
  
  
          if (optional($user)->two_factor_confirmed &&
            ! is_null(optional($user)->two_factor_confirmed_at)) {
  
          
              $confirmed = app(ConfirmPassword::class)(
                Auth::guard() , $request->user(), $request->input('password')
              );
                
             // dd($confirmed);
              if ($confirmed) {
                  $message = __('The provided password is correct.');
                  // $request->session()->put('auth.password_confirmed_at', time());
         
                //  $result = call_user_func_array(array(TwoFactorAuthenticationController::class, 'destroy'), array($request, 'parameter two'));
            
                  $user->forceFill([
                    'two_factor_email_confirmed' => 0,
                    'two_factor_email_confirmed_at' => null,
                  ])->save();
      
       
              }else{
                  $message = __('The provided password was incorrect.');
              }
      
              return $confirmed
                          ? $this->sendRes('Email OTP Successfully Disabled', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])])
                          : response()->json([ 'message' =>  $message, 'errors'=>array('password'=> [$message])], 422);
  
          } else {
            $message = __('You are not allowed to perform this action.');
             return  response()->json([ 'message' =>  $message, 'errors'=>array('password'=> [$message])], 422);
          }
  
  
        }    

}