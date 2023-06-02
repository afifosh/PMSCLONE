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
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Services\CRM\Settings\SettingsService;
use App\Console\Commands\ClearCacheCommand;
use App\Console\Commands\OptimizeCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Modules\Core\Console\Commands\ClearUpdaterTmpPathCommand;
use Modules\Core\Facades\Innoclapps;
use Modules\Core\Settings\DefaultSettings;
use Modules\Translator\Console\Commands\GenerateJsonLanguageFileCommand;
use Illuminate\Contracts\Foundation\Application;

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
    // Vite::useScriptTagAttributes(fn (string $src, string $url, array|null $chunk, array|null $manifest) => [
    //   'onload' => $src === 'resources/js/app.js' ? 'bootApplication()' : false,
    // ]);
    Vite::useScriptTagAttributes(function(string $src, string $url, array|null $chunk, array|null $manifest){
      dd($src);
    });
    Paginator::useBootstrap();
    if (config('app.force_https')) {
      \URL::forceScheme('https');
      $url->formatScheme('https');
    }

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
    JsonResource::withoutWrapping();

    $this->configureUpdater();

    Innoclapps::whenInstalled($this->configureBroadcasting(...));

    DefaultSettings::add('disable_password_forgot', false);

    View::composer('*', \Modules\Core\Http\View\Composers\AppComposer::class);

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
  }

   /**
     * Set the broadcasting driver
     */
    protected function configureBroadcasting(): void
    {
        if (Innoclapps::hasBroadcastingConfigured()) {
            $this->app['config']->set('broadcasting.default', 'pusher');
        }
    }

  /**
     * Configure the core updater.
     */
    protected function configureUpdater(): void
    {
        $this->app['config']->set('updater.optimize', OptimizeCommand::class);

        $this->app['config']->set('updater.commands.post_update', [
            ClearCacheCommand::class,
            ClearUpdaterTmpPathCommand::class,
            [
                'class' => MigrateCommand::class,
                'params' => ['--force' => true],
            ],
        ]);

        $this->app['config']->set('updater.commands.finalize', [
            ClearCacheCommand::class,
            GenerateJsonLanguageFileCommand::class,
        ]);
    }
}
