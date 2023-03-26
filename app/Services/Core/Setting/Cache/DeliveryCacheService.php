<?php

namespace App\Services\Core\Setting\Cache;

use App\Services\Core\Contracts\CachesConfiguration;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DeliveryCacheService extends BaseCacheService implements CachesConfiguration
{
    /**
     * Name of cached key
     * 
     * @var string
     */
    protected static $CACHE_KEY = 'app-delivery-settings';

    /**
     * Name of default provider
     * 
     * @var string
     */
    protected static $PROVIDER = 'default_mail';

    /**
     * Name of default driver 
     * 
     * @var string
     */
    protected static $DRIVER = 'default_mail_email_name';

    /**
     * List of available providers
     * 
     * @var array
     */
    protected $providers = [
        'amazon_ses' => \App\Services\Core\Setting\Delivery\Configuration\Amazon::class,
        'mailgun' => \App\Services\Core\Setting\Delivery\Configuration\Mailgun::class,
        'smtp' => \App\Services\Core\Setting\Delivery\Configuration\SMTP::class,
        'mailtrap' => \App\Services\Core\Setting\Delivery\Configuration\SMTP::class,
        'sendmail' => \App\Services\Core\Setting\Delivery\Configuration\Sendmail::class,
    ];

    /**
     * Cache updated settings
     * 
     * @return void
     */
    public static function handle(): void
    {
        static::update(static::$CACHE_KEY, static::$PROVIDER, static::$PROVIDER);
    }

    /**
     * Loads up delivery configurations
     * 
     * @return void
     */
    public function load()
    {
        $cached = $this->cachedSettings(static::$CACHE_KEY);

        if (is_null($cached)) {
            if (!$this->update(static::$CACHE_KEY, static::$PROVIDER, static::$PROVIDER)) {
                return;
            }
        }

        $this->loadAddress($cached);

        $this->loadProvider($cached, $this->providers, 'smtp', 'provider');
    }

    /**
     * Loads delivery address such as name and email
     * 
     * @param $configurations
     * @return void
     */
    protected function loadAddress($configurations): void
    {
        // config()->set('mail.default', $configurations['provider'] ?? config('mail.default'));
        config()->set('mail.from.address', $configurations['from_email'] ?? config('mail.from.address'));
        config()->set('mail.from.name', $configurations['from_name'] ?? config('mail.from.name'));
    }
}
