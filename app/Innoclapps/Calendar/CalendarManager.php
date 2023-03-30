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

namespace App\Innoclapps\Calendar;

use InvalidArgumentException;
use App\Innoclapps\OAuth\AccessTokenProvider;
use App\Innoclapps\Contracts\OAuth\Calendarable;
use App\Innoclapps\Calendar\Google\GoogleCalendar;
use App\Innoclapps\Calendar\Outlook\OutlookCalendar;

class CalendarManager
{
    /**
     * Create calendar client
     *
     * @param string $connectionType Outlook, Google
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     *
     * @return \App\Innoclapps\Contracts\OAuth\Calendarable
     */
    public static function createClient(string $connectionType, AccessTokenProvider $token) : Calendarable
    {
        $method = 'create' . ucfirst($connectionType) . 'Driver';

        if (! method_exists(new static, $method)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve [%s] driver for [%s].',
                $method,
                static::class
            ));
        }

        return self::$method($token);
    }

    /**
     * Create the Google calendar driver
     *
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     *
     * @return \App\Innoclapps\Calendar\Google\Calendar
     */
    public static function createGoogleDriver(AccessTokenProvider $token) : GoogleCalendar & Calendarable
    {
        return new GoogleCalendar($token);
    }

    /**
     * Create the Outlook calendar driver
     *
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     *
     * @return \App\Innoclapps\Calendar\Outlook\Calendar
     */
    public static function createOutlookDriver(AccessTokenProvider $token) : OutlookCalendar & Calendarable
    {
        return new OutlookCalendar($token);
    }
}
