<?php

namespace App\Services\Core\Setting\Cache;

use App\Repositories\SettingRepository;
use App\Services\Core\Setting\DeliverySettingService;
use Exception;
use Illuminate\Support\Facades\Schema;

class BaseCacheService
{
    /**
     * Updates the cache
     *
     * @param string $cacheKey
     * @param string $provider
     * @param string $service
     *
     * @return bool
     */
    public static function update(string $cacheKey, string $provider, string $service): bool
    {
        // if the database does not have a table. For example if it is migrate:fresh 'ed then issues occur so check them
        if (!Schema::hasTable('settings')) {
            return false;
        }

        cache()->store(config('cache.default'))->put(
            $cacheKey,
            (new static)->getDefault($provider, $service)
        );

        return true;
    }

    /**
     * Get default delivery setting
     *
     * @param $provider
     * @param $service
     *
     * @return array
     */
    protected function getDefault($provider, $service): array
    {
        return app(SettingRepository::class)->getDeliverySettingLists([
            optional(
                app(DeliverySettingService::class)->getDefaultSettings($provider)
            )->value,
            $service
        ]);
    }

    /**
     * Get cached settings
     *
     * @return mixed
     */
    protected function cachedSettings(string $key): mixed
    {
        return cache()->store(
            config('cache.default')
        )->get($key);
    }

    /**
     * Loads up default provider in config
     *
     * @param $configurations
     * @param $providers
     * @param $default
     * @param $key
     *
     * @return void
     */
    protected function loadProvider($configurations, $providers, $provider, $key): void
    {
        $provider = $this->resolveProvider($configurations, $providers, $provider, $key);
        try {
            app($providers[$provider])->load($configurations);
        } catch (Exception $e) {
            // throw new FileNotFoundException("{$this->providers[$configurations['provider']]} is not found");
        }
    }

    /**
     * Resolve the provider & if it does not exist defaults to smtp
     *
     * @param $configurations
     * @param $providers
     * @param $default
     * @param $key
     *
     * @return string
     */
    protected function resolveProvider($configurations, $providers, $default, $key): string
    {
        if (!isset($configurations[$key]) || !array_key_exists($configurations[$key], $providers)) {
            return $default;
        }

        return $configurations[$key];
    }
}
