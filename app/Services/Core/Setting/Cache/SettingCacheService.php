<?php

namespace App\Services\Core\Setting\Cache;

use App\Repositories\SettingRepository;
use Illuminate\Support\Facades\Schema;

class SettingCacheService extends BaseCacheService
{
    private static $cacheManager = [
        'app' => [
            'key' => 'app-general-settings',
            'handler' => \App\Services\Core\Setting\ConfigManager\General::class,
        ],
        'security' => [
            'key' => 'app-security-settings',
            'handler' => \App\Services\Core\Setting\ConfigManager\Security::class,
        ],
        'onlyoffice' => [
            'key' => 'app-onlyoffice-settings',
            'handler' => \App\Services\Core\Setting\ConfigManager\OnlyOffice::class,
        ]        
    ];

    /**
     * Handles updating the cache
     * 
     * @return void
     */
    public static function handle($context): void
    {
        if (isset(static::$cacheManager[$context]) == false) {
            return;
        }

        static::update($context, static::$cacheManager[$context]['key']);
    }

    /**
     * Loads cache to config
     * 
     * @return void
     */
    public function load($contexts): void
    {
        foreach ($contexts as $context) {
            if (!array_key_exists($context, static::$cacheManager)) {
                continue;
            }

            $cacheKey = static::$cacheManager[$context]['key'];

            $cached = $this->cachedSettings($cacheKey);

            if (is_null($cached)) {
                if (!self::update($cacheKey, $context)) {
                    continue;
                }

                $cached = $this->cachedSettings($cacheKey);
            }

            app(static::$cacheManager[$context]['handler'])->load($cached);
        }
    }

    /**
     * Loads cache to config
     * 
     * @return mixed
     */
    public static function update($context, $key, $service = ''): bool
    {
        // if the database does not have a table. For example if it is migrate:fresh 'ed then issues occur so check them
        if (! Schema::hasTable('settings')) {
            return false;
        }

        cache()->store(config('cache.default'))->put(
            $key,
            app(SettingRepository::class)->getFormattedSettings($context)
        );

        return true;
    }
}
