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

namespace App\Innoclapps;

use DateTime;
use Exception;
use DateTimeZone;
use JsonSerializable;
use App\Innoclapps\Contracts\Localizeable;
use Illuminate\Contracts\Support\Arrayable;

class Timezone implements Arrayable, JsonSerializable
{
    /**
     * @param integer $timestamp
     * @param string $timezone
     * @param string $format
     *
     * @return string
     */
    public function convertFromUTC($timestamp, $timezone = null, $format = 'Y-m-d H:i:s') : string
    {
        $timezone ??= $this->current();

        $date = new DateTime($timestamp, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone($timezone));

        return $date->format($format);
    }

    /**
     * @param integer $timestamp
     * @param string $timezone
     * @param string $format
     *
     * @return string
     */
    public function convertToUTC($timestamp, $timezone = null, $format = 'Y-m-d H:i:s') : string
    {
        $timezone ??= $this->current();

        $date = new DateTime($timestamp, new DateTimeZone($timezone));
        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format($format);
    }

    /**
     * Alias to convertToUTC
     *
     * @param mixed $params
     *
     * @return string
     */
    public function toUTC(...$params) : string
    {
        return $this->convertToUTC(...$params);
    }

    /**
     * Alias to convertFromUTC
     *
     * @param mixed
     *
     * @return string
     */
    public function fromUTC(...$params) : string
    {
        return $this->convertFromUTC(...$params);
    }

    /**
     * Get the current timezone for the appliaction
     *
     * @param null|\App\Innoclapps\Contracts\Localizeable $user
     *
     * @return string
     */
    public function current(?Localizeable $user = null) : string
    {
        $user ??= auth()->user();

        // In case using the logged in user, check the interface
        throw_if(
            $user && ! $user instanceof Localizeable,
            new Exception('The user must be instance of ' . Localizeable::class)
        );

        return $user ? $user->getUserTimezone() : config('app.timezone');
    }

    /**
     * Get all timezones
     *
     * @return array
     */
    public function all() : array
    {
        return DateTimeZone::listIdentifiers(DateTimeZone::ALL);
    }

    /**
     * jsonSerialize
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    /**
     * Timezones to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }
}
