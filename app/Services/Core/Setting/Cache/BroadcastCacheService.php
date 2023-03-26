<?php

namespace App\Services\Core\Setting\Cache;

use App\Services\Core\Contracts\CachesConfiguration;
use App\Services\Core\Setting\Cache\BaseCacheService;

class BroadcastCacheService extends BaseCacheService implements CachesConfiguration
{

    /**
     * Name of cached key
     * 
     * @var string
     */
    protected static $CACHE_KEY = 'app-broadcast-settings';

    /**
     * Name of default provider
     * 
     * @var string
     */
    protected static $PROVIDER = 'default_broadcast';

    /**
     * Name of default driver 
     * 
     * @var string
     */
    protected static $DRIVER = 'default_broadcast_driver_name';

    /**
     * List of available providers
     * 
     * @var array
     */
    protected $providers = [
        'pusher' => \App\Services\Core\Setting\Broadcast\ProviderConfiguration\Pusher::class,
    ];

    /**
     * Handles updating the cache
     * 
     * @return void
     */
    public static function handle(): void
    {
        static::update(static::$CACHE_KEY, static::$PROVIDER, static::$DRIVER);
    }

    /**
     * Loads cache to config
     * 
     * @return void
     */
    public function load(): void
    {
        $cached = $this->cachedSettings(static::$CACHE_KEY);

        if (is_null($cached)) {
            if (!$this->update(static::$CACHE_KEY, static::$PROVIDER, static::$DRIVER)) {
                return;
            }
        }

        $this->loadProvider($cached, $this->providers, 'ajax', 'broadcast_driver');
    }
}
