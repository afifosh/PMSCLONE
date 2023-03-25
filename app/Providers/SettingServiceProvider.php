<?php

namespace App\Providers;

use App\Services\Core\Setting\Cache\BroadcastCacheService;
use App\Services\Core\Setting\Cache\DeliveryCacheService;
use App\Services\Core\Setting\Cache\SettingCacheService;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class SettingServiceProvider extends ServiceProvider
{


    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->cacheSettings();
    }

    /**
     * Loads cache into config
     * 
     * @return void
     */
    private function cacheSettings(): void
    {
        Collection::make([
            DeliveryCacheService::class,
            BroadcastCacheService::class
        ])->each(fn ($service) => app($service)->load());

        app(SettingCacheService::class)->load(
            ['app', 'security']
        );
    }
}
