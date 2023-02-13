<?php

namespace App\Providers;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
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
        
        if (Schema::hasTable('app_settings')) {
            $app_setting = AppSetting::first();
        }

        $timeout_warning_seconds = isset($app_setting) && ! is_null($app_setting) &&
            ! is_null($app_setting->timeout_warning_seconds) ? $app_setting->timeout_warning_seconds
            : config('auth.timeout_warning_seconds');

        $timeout_after_seconds = isset($app_setting) && ! is_null($app_setting) &&
            ! is_null($app_setting->timeout_after_seconds) ? $app_setting->timeout_after_seconds
            : config('auth.timeout_after_seconds');
            
        Cache::put('timeout_warning_seconds', $timeout_warning_seconds);
        Cache::put('timeout_after_seconds', $timeout_after_seconds);
    }
}
