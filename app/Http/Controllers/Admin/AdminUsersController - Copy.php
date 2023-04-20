<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AdminsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CompanyDepartment;
use App\Models\CompanyDesignation;
use App\Models\PartnerCompany;
use App\Models\Role;
use App\Notifications\Admin\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Events\TwoFactorCodeEvent;
Use \Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Session;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;


class AdminUsersController extends Controller
{

  function __construct()
  {
    $this->middleware('permission:read user|create user|update user|delete user', ['only' => ['index', 'show']]);
    $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete user', ['only' => ['destroy']]);
    $this->middleware('permission:impersonate user', ['only' => ['impersonate']]);
  }

  public function index(AdminsDataTable $dataTable)
  {
    // $data['roles'] = Role::where('guard_name', 'admin')->with('users')->withCount('users')->get();
    $data['partners'] = PartnerCompany::distinct()->pluck('name', 'id');
    $data['roles'] = Role::where('guard_name', 'admin')->distinct()->pluck('name');
    $data['statuses'] = Admin::distinct()->pluck('status');
    return $dataTable->render('admin.pages.partner.employees.index', $data);
        // return view('admin.pages.partner.employees.index', $data);
  }

  public function create()
  {
    $data['user'] = new Admin();
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend(__('Select Organization'), '');
    $data['departments'] = ['' => 'Select Department'];
    $data['designations'] = ['' => 'Select Designation'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', 'unique:admins,email'],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id',
      'email_verified_at' => 'sometimes',
    ],[
      'company_id.required' => __('Company field is required'),
      'department_id.required' => __('Department field is required'),
      'designation_id.required' => __('Designation field is required'),
    ]);
    unset($att['roles']);
    // if ($request->password) {
    //   $att['password'] = Hash::make($att['password']);
    // } else {
    //   unset($att['password']);
    // }
    $password = Str::random(15);
    $att['password'] = Hash::make($password);
    $att['email_verified_at'] = $request->boolean('email_verified_at') ? now() : null;
    $user = Admin::create($att);
    $user->notify(new WelcomeNotification($password));
    $user->syncRoles($request->roles);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
  }

  public function show(Admin $user)
  {
    return view('admin.pages.roles.admins.show', compact('user'));
  }

  public function edit(Admin $user)
  {
    $data['user'] = $user;
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend('Select Organization', '');
    $data['departments'] = CompanyDepartment::where('id', @$user->designation->department_id)->pluck('name', 'id')->prepend('Select Department', '');
    $data['designations'] = CompanyDesignation::where('id', $user->designation_id)->pluck('name', 'id')->prepend('Select Designation', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  public function editPassword(Admin $user)
  {
    $data['user'] = $user;
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit-password', $data)->render()]);
  }

  public function updatePassword(Request $request, Admin $user)
  {
    $request->validate([
      'password' => 'required|min:8|max:255|confirmed',
    ]);
    $user->update(['password' => Hash::make($request->password)]);
    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
  }

  public function update(Request $request, Admin $user)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', Rule::unique('admins')->ignore($user->id),],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id',
      'email_verified_at' => 'sometimes'
    ]);
    unset($att['roles']);
    if($user->email_verified_at && $request->boolean('email_verified_at')){
      unset($att['email_verified_at']);
    }else if(!$user->email_verified_at && $request->boolean('email_verified_at')){
      $att['email_verified_at'] = now();
    }else if(!$request->boolean('email_verified_at')){
      $att['email_verified_at'] = null;
    }
    $user->syncRoles($request->roles);
    if ($user->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
    }
  }

  public function destroy(Admin $user)
  {
    if ($user->id == 1)
      return $this->sendError('This User Cannot be deleted');
    if ($user->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table']);
    }
  }

  public function impersonate(Admin $user)
  {
    auth('admin')->user()->impersonate($user, 'admin');

    return back()->with('success', 'impersonated');
  }

  public function leaveImpersonate()
  {
    auth('admin')->user()->leaveImpersonation();

    return back()->with('success', 'Impersonation Removed');
  }
  public function sendEmailOTPVerificationCode(Admin $user)
  {
    $throttleKey = 'email-verification:' . strtolower($user->email);
    RateLimiter::hit($throttleKey);
    $user->generateTwoFactorCode();
    event(new TwoFactorCodeEvent($user));

  }

  public function resendCode(Request $request)
  {

    // $ip = $request->ip();
    // $cacheKey = "otp-request:{$ip}";
    $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');
    $cacheKey = 'otp_sent_at_' . $user->id . '_'.$request->ip();
    $ttl = 60; // 60 seconds
    $expireAt = now()->addMinutes(1);
   if (Cache::add($cacheKey, now(), $expireAt)) {
    $this->sendEmailOTPVerificationCode($user);
    return $this->sendRes('Email OTP Code Sent to  '.$user->email);
   }else{
    // Wait for 60 seconds before sending another OTP code
    $secondsSinceLastOtpSent = $ttl - now()->diffInSeconds(Cache::get($cacheKey));
    // $secondsSinceLastOtpSent = Cache::get($cacheKey)->diffInSeconds(now());
    // $secondsSinceLastOtpSent = Cache::get($cacheKey);
  //  $secondsToWait = $ttl - $secondsSinceLastOtpSent;
   // abort(429, 'Too many requests. Please wait ' .     $secondsSinceLastOtpSent  . ' seconds before trying again.');
        $msg = 'Too many requests. Please wait ' .     $secondsSinceLastOtpSent  . ' seconds before trying again.';
        return response()->json(['success' => false, 'message' =>  $msg, 'data'=>$msg], 422);
        return $this->sendError('Something went wrong', ['error' => $msg]);
   }

    // if (Cache::has($cacheKey)) {
    //     $secondsRemaining = Cache::get($cacheKey);
    //     return response()->json(['error' => "You can't request another OTP code yet. Please try again in {$secondsRemaining} seconds."]);
    // }

    // // Send the OTP code here

    // Cache::put($cacheKey, 60, 60);

    // return response()->json(['message' => 'OTP code sent.']);

    // $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');

    // // dd(  $user);
    // $rateLimiterKey = 'email-verification:' . strtolower($user->email);

    //   $resendSmsTimeoutSecs = 60;
    //   $attempt = RateLimiter::attempt($rateLimiterKey, 1,
    //       function () use ($user) {
    //         $this->sendEmailOTPVerificationCode($user);
    //       },
    //       $resendSmsTimeoutSecs
    //   );
      
  
      
  
    //   if(!$attempt){
    //     $msg = 'Rate limit is exceeded. Try again in ' .RateLimiter::availableIn($rateLimiterKey). ' seconds..';
    //     return response()->json(['success' => false, 'message' =>  $msg, 'data'=>$msg], 422);
    //     return $this->sendError('Something went wrong', ['error' => $msg]);
    //    // return $this->sendRes($msg);
    //       }else{
    //         return $this->sendRes('Email OTP Code Sent to  '.$user->email);
  
    //   }

    //   return $this->sendRes('Email OTP Code Sent to  '.$user->email);
  }

  public function resendCodeolddd(Request $request)
  {
    $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');

    // dd(  $user);
    $rateLimiterKey = 'email-verification:' . strtolower($user->email);

      $resendSmsTimeoutSecs = 60;
      $attempt = RateLimiter::attempt($rateLimiterKey, 1,
          function () use ($user) {
            $this->sendEmailOTPVerificationCode($user);
          },
          $resendSmsTimeoutSecs
      );
      
  
      
  
      if(!$attempt){
        $msg = 'Rate limit is exceeded. Try again in ' .RateLimiter::availableIn($rateLimiterKey). ' seconds..';
        return response()->json(['success' => false, 'message' =>  $msg, 'data'=>$msg], 422);
        return $this->sendError('Something went wrong', ['error' => $msg]);
       // return $this->sendRes($msg);
          }else{
            return $this->sendRes('Email OTP Code Sent to  '.$user->email);
  
      }

      return $this->sendRes('Email OTP Code Sent to  '.$user->email);
  }

  public function VerifyCode(Request $request)
  {   
  //   $request->validate([
  //     'code' => 'required',
  // ]);

  // {{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])}}
 // return $this->sendRes('File Shared Successfully', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
 // return $this->sendRes('Email OTP Successfully Enabled', ['event' => 'page_reload', 'close' => 'modal']);
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
      $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');

    // dd($request->endpoint);
     // $user = Auth::user();

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
      // return redirect()->route('admin.user.profile.settings', ['active_tab' => $request->active_tab]);
    //  return redirect()->route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security']);
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
        $confirm($request->user(), $request->input('code'));
        
        return $this->sendRes('Two Factor Authentication Successfully Activated', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
         
        // Session::flash('showingQrCode', false);
        // Session::flash('showingRecoveryCodes', false);
        // Session::flash('success', 'Successfully Disabled Two Factor Authentication.');
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

        return $this->sendRes('Two Factor Authentication Successfully Disabled', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
         
        // Session::flash('showingQrCode', false);
        // Session::flash('showingRecoveryCodes', false);
        // Session::flash('success', 'Successfully Disabled Two Factor Authentication.');
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

       return $this->sendRes('Two Factor Authentication Successfully Enabled', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
        // Session::flash('showingQrCode', true);
        // Session::flash('showingRecoveryCodes', true);
        // Session::flash('success', 'Successfully Disabled Two Factor Authentication.');
    }  
    
    
    public function enableEmailTwoFactorAuthentication($user,Request $request)
    {   
    //   $request->validate([
    //     'code' => 'required',
    // ]);
  
    // {{ route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])}}
   // return $this->sendRes('File Shared Successfully', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])]);
   // return $this->sendRes('Email OTP Successfully Enabled', ['event' => 'page_reload', 'close' => 'modal']);
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
     * Confirm the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function disableEmailTwoFactorAuthentication($user,Request $request)
    {
      $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');



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
    
            return $confirmed
                        ? $this->sendRes('Email OTP Successfully Disabled', ['event' => 'redirect', 'url' => route('admin.admin-account.edit', ['admin_account' => auth()->id(), 't' => 'security'])])
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
    public function twoFactorGoogle(Request $request)
    {
      $user =  Session::has('login.id') ? Admin::findOrFail(Session::get('login.id')) : $request->user('admin');




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