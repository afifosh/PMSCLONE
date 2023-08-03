<?php

namespace App\Providers;

use App\Helpers\CRM\Traits\SetAppSecurityConfig;
use Illuminate\Pagination\Paginator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use App\Helpers\CRM\Traits\SetBroadcastingConfig;
use App\Helpers\CRM\Traits\SetMailConfig;
use App\Helpers\CRM\Traits\SetOnlyOfficeConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Contracts\Foundation\Application;
use Opcodes\LogViewer\Facades\LogViewer;

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
    // Model::preventLazyLoading(true);
    Paginator::useBootstrap();
    if (config('app.force_https')) {
      \URL::forceScheme('https');
      $url->formatScheme('https');
    }

    // Schema::defaultStringLength(191);

    // setLocale for php. Enables ->formatLocalized() with localized values for dates
    // setlocale(LC_TIME, config('app.locale_php'));

    Carbon::setLocale(config('app.locale'));
    JsonResource::withoutWrapping();

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

    $this->app->booted(function (Application $app) {
      $app->register(ModulesConfigServiceProvider::class);
    });

    LogViewer::auth(function ($request) {
      return auth()->id() == 1;
     });
  }
}
