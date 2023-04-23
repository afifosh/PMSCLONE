<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Innoclapps\Settings;

use App\Innoclapps\Facades\Innoclapps;
use Illuminate\Support\ServiceProvider;
use App\Innoclapps\Settings\Contracts\Store as StoreContract;
use App\Innoclapps\Settings\Contracts\Manager as ManagerContract;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton(ManagerContract::class, SettingsManager::class);

        $this->app->singleton(StoreContract::class, function ($app) {
            return $app[ManagerContract::class]->driver();
        });

        $this->app->extend(ManagerContract::class, function (ManagerContract $manager, $app) {
            foreach ($app['config']->get('setting.drivers', []) as $driver => $params) {
                $manager->registerStore($driver, $params);
            }

            return $manager;
        });
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        // if (! Innoclapps::isAppInstalled()) {
        //     return;
        // }

        $this->app[ManagerContract::class]->driver()->configureOverrides();
    }
}
