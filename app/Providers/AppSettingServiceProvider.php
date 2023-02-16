<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppSettingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $app_settings = cache()->store(config('cache.default'))->get('idle_timeout_settings');

        if (is_null($app_settings)) {
            $this->cacheAppSettings();
        }

        $app_settings = json_decode($app_settings);

        config()->set('auth.timeout_warning_seconds', $app_settings->timeout_warning_seconds ?? config('auth.timeout_warning_seconds'));
        
        config()->set('auth.timeout_after_seconds', $app_settings->timeout_after_seconds ?? config('auth.timeout_after_seconds'));
    }

    private function cacheAppSettings()
    {
        if (Schema::hasTable('app_settings')) {
            $app_setting = AppSetting::first();
        }

        $timeout_warning_seconds = isset($app_setting) && !is_null($app_setting) &&
            !is_null($app_setting->timeout_warning_seconds) ? $app_setting->timeout_warning_seconds
            : config('auth.timeout_warning_seconds');

        $timeout_after_seconds = isset($app_setting) && !is_null($app_setting) &&
            !is_null($app_setting->timeout_after_seconds) ? $app_setting->timeout_after_seconds
            : config('auth.timeout_after_seconds');

        cache()->store(config('cache.default'))->put(
            'idle_timeout_settings',
            json_encode(
                array(
                    'timeout_warning_seconds'   => $timeout_warning_seconds,
                    'timeout_after_seconds'     => $timeout_after_seconds,
                )
            ),
        );
    }
}