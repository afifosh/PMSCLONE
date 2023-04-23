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

namespace App\Innoclapps\Settings\Stores;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Artisan;
use App\Innoclapps\Settings\Utilities\Arr;
use App\Innoclapps\Settings\Contracts\Store;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Encryption\DecryptException;

abstract class AbstractStore implements Store
{
    /**
    * The settings data.
    *
    * @var array
    */
    protected array $data = [];

    /**
     * Original settings
     *
     * @var array
     */
    protected array $original = [];

    /**
     * @var array
     */
    protected array $overrides;

    /**
     * The settings keys that should be encrypted in storage
     *
     * @var array
     */
    protected array $encrypted;

    /**
    * Whether the store has changed since it was last loaded.
    *
    * @var boolean
    */
    protected bool $unsaved = false;

    /**
    * Whether the settings data are loaded.
    *
    * @var boolean
    */
    protected bool $loaded = false;

    /**
    * AbstractStore constructor.
    *
    * @param \Illuminate\Contracts\Foundation\Application $app
    * @param array $options
    */
    public function __construct(protected Application $app, array $options = [])
    {
        $this->overrides = $app['config']->get('settings.override', []);
        $this->encrypted = $app['config']->get('settings.encrypted', []);

        $this->postOptions($options);
    }

    /**
    * Fire the post options to customize the store.
    *
    * @param array $options
    */
    abstract protected function postOptions(array $options);

    /**
    * Read the data from the store.
    *
    * @return array
    */
    abstract protected function read() : array;

    /**
    * Write the data into the store.
    *
    * @param array $data
    *
    * @return void
    */
    abstract protected function write(array $data) : void;

    /**
    * Get a specific key from the settings data.
    *
    * @param string $key
    * @param mixed $default
    *
    * @return mixed
    */
    public function get(string $key, mixed $default = null) : mixed
    {
        $this->checkLoaded();

        $value = Arr::get($this->data, $key, $default);

        return $this->parseValue($value, $key);
    }

    /**
    * Determine if a key exists in the settings data.
    *
    * @param string $key
    *
    * @return boolean
    */
    public function has(string $key) : bool
    {
        $this->checkLoaded();

        return Arr::has($this->data, $key);
    }

    /**
    * Set a specific key to a value in the settings data.
    *
    * @param string|array $key
    * @param mixed $value
    *
    * @return static
    */
    public function set(string|array $key, mixed $value = null) : static
    {
        $this->checkLoaded();

        $this->unsaved = true;

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setValue($k, $v);
            }
        } else {
            $this->setValue($key, $value);
        }

        return $this;
    }

    /**
    * Unset a key in the settings data.
    *
    * @param string $key
    *
    * @return static
    */
    public function forget(string $key) : static
    {
        $this->checkLoaded();

        $this->unsaved = true;

        Arr::forget($this->data, $key);

        return $this;
    }

    /**
    * Flushing all data.
    *
    * @return static
    */
    public function flush() : static
    {
        $this->unsaved  = true;
        $this->data     = [];
        $this->original = [];

        return $this;
    }

    /**
    * Get all settings data.
    *
    * @return array
    */
    public function all() : array
    {
        $this->checkLoaded();

        return Arr::map($this->data, fn ($value, $key) => $this->parseValue($value, $key));
    }

    /**
    * Save any changes done to the settings data.
    *
    * @return static
    */
    public function save() : static
    {
        if (! $this->isSaved()) {
            $this->write($this->data);
            $this->recacheConfigIfOverridesChanged();
            $this->original = $this->data;
            $this->unsaved  = false;
            $this->configureOverrides();
        }

        return $this;
    }

    /**
    * Configure the settings overrides for Laravel configuration.
    *
    * @return void
    */
    public function configureOverrides() : void
    {
        foreach (Arr::dot($this->overrides) as $configKey => $settingKey) {
            $configKey = $configKey ?: $settingKey;
            $value     = $this->get($settingKey);

            if (! is_null($value)) {
                config()->set([$configKey => $value]);
            }
        }
    }

    /**
     * Reache the config if settings that overrides config has changed
     *
     * The config may be cached with overrides set and when the settings are updated
     * e.q set to null, the Laravel config won't be updated because only non-null values are overridden
     *
     * @return void
     */
    protected function recacheConfigIfOverridesChanged() : void
    {
        if (! file_exists($this->app->getCachedConfigPath())) {
            return;
        }

        $recache = false;

        foreach ($this->overrides as $settingKey) {
            // Was null
            if (! array_key_exists($settingKey, $this->original)) {
                // Is value set now?
                if (! is_null($this->get($settingKey))) {
                    $recache = true;

                    break;
                }

                // Not modified, as it does not exists in data
                continue;
            }

            // Value changed?
            if ($this->original[$settingKey] !== $this->get($settingKey)) {
                $recache = true;

                break;
            }
        }

        if ($recache) {
            Artisan::call('config:cache');
        }
    }

    /**
    * Check if the data is saved.
    *
    * @return boolean
    */
    public function isSaved() : bool
    {
        return ! $this->unsaved;
    }

    /**
     * Parse the given value
     *
     * @param string|null $value
     * @param string $key
     *
     * @return mixed
     */
    protected function parseValue($value, $key)
    {
        if (in_array($key, $this->encrypted) && ! empty($value)) {
            try {
                return Crypt::decryptString($value);
            } catch (DecryptException $e) {
                return null;
            }
        }

        return $value;
    }

    /**
     * Set the value to the store
     *
     * @param string $key
     * @param string $value
     *
     * @return void
     */
    protected function setValue($key, $value) : void
    {
        if (in_array($key, $this->encrypted) && ! empty($value)) {
            $value = Crypt::encryptString($value);
        }

        Arr::set($this->data, $key, $value);
    }

    /**
    * Check if the settings data has been loaded.
    */
    protected function checkLoaded() : void
    {
        if ($this->isLoaded()) {
            return;
        }

        $this->data     = $this->read();
        $this->original = $this->data;
        $this->loaded   = true;
    }

    /**
    * Reset the loaded status.
    */
    protected function resetLoaded() : void
    {
        $this->loaded = false;
    }

    /**
    * Check if the data is loaded.
    *
    * @return boolean
    */
    protected function isLoaded() : bool
    {
        return $this->loaded;
    }

    /**
    * TODO: Remove in future, causes issue during update when updating to v1.0.6
    *
    * @deprecated 1.0.6
    */
    public static function setOverrides() : void
    {
    }

    /**
    * TODO: Remove in future, causes issue during update when updating to v1.0.6
    *
    * @deprecated 1.0.6
    */
    protected static function getOverrideValue(string $key) : mixed
    {
    }
}
