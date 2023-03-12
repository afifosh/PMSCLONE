<?php

namespace App\Traits;

use App\Repositories\SettingRepository;
use App\Services\Core\Setting\Delivery\Configuration\Amazon;
use App\Services\Core\Setting\Delivery\Configuration\Mailgun;
use App\Services\Core\Setting\Delivery\Configuration\Sendmail;
use App\Services\Core\Setting\Delivery\Configuration\SMTP;
use App\Services\Core\Setting\Delivery\DeliverySettingService;
use Exception;
use Illuminate\Support\Facades\Schema;

trait DeliverySetting
{
    /**
     * List of available providers
     * 
     * @var array
     */
    private $providers = [
        'amazon_ses' => Amazon::class,
        'mailgun' => Mailgun::class,
        'smtp' => SMTP::class,
        'mailtrap' => SMTP::class,
        'sendmail' => Sendmail::class,
    ];

    /**
     * Get default delivery setting
     * 
     * @return array
     */
    private function getDefault(): array
    {
        return app(SettingRepository::class)->getDeliverySettingLists([
            optional(
                app(DeliverySettingService::class)->getDefaultSettings('default_mail')
            )->value,
            'default_mail_email_name'
        ]);
    }

    /**
     * Cache updated settings
     * 
     * @return bool
     */
    public function updateCache(): bool
    {
        // if the database does not have a table. For example if it is migrate:fresh 'ed then issues occur so check them
        if (! Schema::hasTable('settings')) {
            return false;
        }

        cache()->store(config('cache.default'))->put(
            'app-delivery-settings',
            $this->getDefault()
        );

        return true;
    }

    /**
     * Loads up delivery configurations
     * 
     * @return void
     */
    public function loadDeliveryConfig(): void
    {
        try {
            $configurations = cache()->store(
                config('cache.default')
            )->get('app-delivery-settings');

            if (is_null($configurations)) {
                $cacheUpdated = $this->updateCache();

                if (! $cacheUpdated) {
                    return;
                }
            }

            $this->loadDeliveryAddress($configurations);

            $this->loadProvider($configurations);
        } catch (Exception $e) {
            //
        }
    }

    /**
     * Loads delivery address such as name and email
     * 
     * @param $configurations
     * @return void
     */
    private function loadDeliveryAddress($configurations): void
    {
        // config()->set('mail.default', $configurations['provider'] ?? config('mail.default'));
        config()->set('mail.from.address', $configurations['from_email'] ?? config('mail.from.address'));
        config()->set('mail.from.name', $configurations['from_name'] ?? config('mail.from.name'));
    }

    /**
     * Loads up default provider in config
     * 
     * @param $configurations
     * @return void
     */
    private function loadProvider($configurations): void
    {
        $provider = $this->resolveProvider($configurations);

        try {
            app($this->providers[$provider])->load($configurations);
        } catch (Exception $e) {
            // throw new FileNotFoundException("{$this->providers[$configurations['provider']]} is not found");
        }
    }

    /**
     * Resolve the provider & if it does not exist defaults to smtp
     * 
     * @param $configurations
     * @return string
     */
    private function resolveProvider($configurations): string
    {
        return array_key_exists(
            $configurations['provider'],
            $this->providers
        ) ? $configurations['provider'] : 'smtp';
    }
}
