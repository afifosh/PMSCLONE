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
    }
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
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

    if(config('fortify.guard' == 'admin')){
      Fortify::viewPrefix('admin.auth.');
    }else{
      Fortify::viewPrefix('auth.');
    }

    Fortify::twoFactorChallengeView(function (Request $request) {
      if (str_contains(session('url.intended'), 'admin')){
        if($request->type != 'recovery-code')
          return view('admin.auth.two-factor-challenge');
        return view('admin.auth.two-factor-challenge-recovery');
      }
      if($request->type != 'recovery-code')
        return view('auth.two-factor-challenge');
      return view('auth.two-factor-challenge-recovery');
    });
  }
}
