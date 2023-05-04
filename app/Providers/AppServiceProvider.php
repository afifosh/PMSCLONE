<?php

namespace App\Providers;

use App\Helpers\CRM\Traits\SetAppSecurityConfig;
use App\Innoclapps\Facades\Innoclapps;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use App\Helpers\CRM\Traits\SetBroadcastingConfig;
use App\Helpers\CRM\Traits\SetMailConfig;
use App\Helpers\CRM\Traits\SetOnlyOfficeConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Services\CRM\Settings\SettingsService;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    if (config('app.force_https')) {
      $this->app['request']->server->set('HTTPS', true);
    }
  }

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot(UrlGenerator $url)
  {
    Paginator::useBootstrap();
    if (config('app.force_https')) {
      \URL::forceScheme('https');
      $url->formatScheme('https');
    }
    Innoclapps::resourcesIn(app_path('Resources'));

    Schema::defaultStringLength(191);
    /*
         * Application locale defaults for various components
         *
         * These will be overridden by LocaleMiddleware if the session local is set
         */

    // setLocale for php. Enables ->formatLocalized() with localized values for dates
    setlocale(LC_TIME, config('app.locale_php'));

    Carbon::setLocale(config('app.locale'));

    try {
      $settings = resolve(SettingsService::class)->getCachedFormattedSettings();
      View::composer('*', function ($view) use ($settings) {
        $view->with('settings', $settings);
      });

      foreach ($settings as $key => $setting) {
        if ($key == 'company_name') {
          config()->set('app.name', $setting);
        }
        config()->set('settings.application.' . $key, $setting);
      }
    } catch (\Exception $exception) {
    }

    try {
      SetMailConfig::new(true)
        ->clear()
        ->set();
    } catch (\Exception $exception) {
    }

    try {
      SetBroadcastingConfig::new(true)
        ->cashClear()
        ->configSet();
    } catch (\Exception $exception) {
    }

    try {
      SetOnlyOfficeConfig::new(true)
        ->cashClear()
        ->configSet();
    } catch (\Exception $exception) {
    }

    try {
      SetAppSecurityConfig::new(true)
        ->cashClear()
        ->configSet();
    } catch (\Exception $exception) {
    }
  }
}
