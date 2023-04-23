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

namespace App\Innoclapps\Facades;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Facade;
use App\Innoclapps\Contracts\OAuth\StateStorage;

class OAuthState extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return StateStorage::class;
    }

    /**
     * Validate the returned state from OAuth
     *
     * @param string $current
     *
     * @return boolean
     */
    public static function validate($current)
    {
        return ! (empty($current)
                || (static::has() && ! static::matches($current)))



         ;
    }

    /**
     * Check whether provided state matches with
     *
     * the one in storage
     *
     * @param string $value
     *
     * @return boolean
     */
    public static function matches($value)
    {
        return $value === static::get();
    }

    /**
     * Create a custom OAuth state with parameters included
     *
     * @return string
     */
    public static function putWithParameters($parameters)
    {
        $state = base64_encode(json_encode($parameters));

        static::put($state);

        return $state;
    }

    /**
     * Get previously passsed paremeter from state
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public static function getParameter($key, $default = null)
    {
        $decoded = base64_decode(static::get());

        // State not valid for params
        if (! Str::isJson($decoded)) {
            return $default;
        }

        $params = json_decode($decoded);

        return $params->{$key} ?? $default;
    }
}
