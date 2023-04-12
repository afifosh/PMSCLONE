<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Features;
use App\Actions\Fortify\CheckDeviceAuthorization;
use App\Actions\Fortify\CaptchaValidations;
use App\Actions\Fortify\TwoFactorEmailOTP;
// Custom created file
use App\Http\Controllers\Auth\RedirectToMailOTP as RedirectToTwoFactorMailOTPAuthentication;
use App\Http\Controllers\Auth\RedirectToDeviceAuthorization as RedirectToTwoFactorRedirectToDeviceAuthorization;
class FortifyServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    if (request()->is('admin/*')) {
      config(['fortify.guard' => 'admin']);
      config(['fortify.passwords' => 'admins']);
      config(['fortify.home' => RouteServiceProvider::ADMIN_HOME]);
      config(['fortify.redirects.logout' => 'admin/login']);
      config('fortify.middleware', ['web', 'guest:web']);
    }

    $this->app->singleton(\Laravel\Fortify\Contracts\LoginResponse::class, \App\Http\Responses\LoginResponse::class);
    $this->app->singleton(\Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse::class, \App\Http\Responses\FailedTwoFactorLoginResponse::class);
    $this->app->singleton(\Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable::class, \App\Actions\Fortify\CustomRedirectIfTwoFactorAuthenticatable::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {


   Fortify::authenticateThrough(function (Request $request) {
        return array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            CheckDeviceAuthorization::class,
            RedirectToTwoFactorMailOTPAuthentication::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            // TwoFactorEmailOTP::class,
            // CaptchaValidation::class,
            // CaptchaValidations::class,
            //RedirectToTwoFactorMailOTPAuthentication::class,
           // Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            //RedirectToTwoFactorRedirectToDeviceAuthorization::class,
      
            AttemptToAuthenticate::class,
        
            PrepareAuthenticatedSession::class,
         
        ]);
    });

    Fortify::createUsersUsing(CreateNewUser::class);
    Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
    Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
    Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

    RateLimiter::for('login', function (Request $request) {
      $email = (string) $request->email;

      return Limit::perMinute(5)->by($email . $request->ip());
    });

    RateLimiter::for('two-factor', function (Request $request) {
      return Limit::perMinute(5)->by($request->session()->get('login.id'));
    });

    if (config('fortify.guard') == 'admin') {
      Fortify::viewPrefix('admin.auth.');
    } else {
      Fortify::viewPrefix('auth.');
    }


    // Fortify::confirmPasswordView(function (Request $request) {
    //   if(config('fortify.guard') == 'admin') {
 
    //     return view('admin.auth.passwords.confirm');
    //   }
    //   return view('auth.passwords.confirm');
    // });

    
    Fortify::twoFactorChallengeView(function (Request $request) {
      if(config('fortify.guard') == 'admin') {
      // if (str_contains(session('url.intended'), 'admin')) {
        if ($request->type != 'recovery-code')
          return view('admin.auth.two-factor-challenge');
        return view('admin.auth.two-factor-challenge-recovery');
      }
      if ($request->type != 'recovery-code')
        return view('auth.two-factor-challenge');
      return view('auth.two-factor-challenge-recovery');
    });
  }
}
