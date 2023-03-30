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

namespace App\Innoclapps\Calendar\Google;

use Google_Service_Exception;
use App\Innoclapps\Facades\Google as Client;
use App\Innoclapps\OAuth\AccessTokenProvider;
use App\Innoclapps\Contracts\OAuth\Calendarable;
use App\Innoclapps\Calendar\Exceptions\UnauthorizedException;

class GoogleCalendar implements Calendarable
{
    /**
     * Initialize new GoogleCalendar instance.
     *
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     */
    public function __construct(protected AccessTokenProvider $token)
    {
        Client::connectUsing($token->getEmail());
    }

    /**
     * Get the available calendars
     *
     * @return \App\Innoclapps\Contracts\Calendar\Calendar[]
     */
    public function getCalendars()
    {
        try {
            return collect(Client::calendar()->list())
                ->mapInto(Calendar::class)
                ->all();
        } catch (Google_Service_Exception $e) {
            $message = $e->getErrors()[0]['message'] ?? $e->getMessage();

            if ($e->getCode() == 403) {
                throw new UnauthorizedException($message, $e->getCode(), $e);
            }

            throw $e;
        }
    }
}
