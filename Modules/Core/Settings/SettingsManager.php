<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Settings;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;
use Modules\Core\Settings\Contracts\Manager as SettingsManagerContract;
use Modules\Core\Settings\Contracts\Store as StoreContract;

class SettingsManager extends Manager implements SettingsManagerContract
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('settings.default', 'json');
    }

    /**
     * Register a new store.
     *
     *
     * @return static
     */
    public function registerStore(string $driver, array $params)
    {
        return $this->extend($driver, function () use ($params): StoreContract {
            return $this->container->make($params['driver'], [
                'options' => Arr::get($params, 'options', []),
            ]);
        });
    }
}
