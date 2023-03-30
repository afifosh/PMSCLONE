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

namespace App\Innoclapps\Calendar\Outlook;

use App\Innoclapps\Facades\Microsoft as Api;
use App\Innoclapps\OAuth\AccessTokenProvider;
use App\Innoclapps\Contracts\OAuth\Calendarable;
use Microsoft\Graph\Model\Calendar as CalendarModel;
use App\Innoclapps\OAuth\Exceptions\ConnectionErrorException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class OutlookCalendar implements Calendarable
{
    /**
     * Initialize new OutlookCalendar instance.
     *
     * @param \App\Innoclapps\OAuth\AccessTokenProvider $token
     */
    public function __construct(protected AccessTokenProvider $token)
    {
        Api::connectUsing($token);
    }

    /**
     * Get the available calendars
     *
     * @return \App\Innoclapps\Contracts\Calendar\Calendar[]
     */
    public function getCalendars()
    {
        $iterator = Api::createCollectionGetRequest('/me/calendars')->setReturnType(CalendarModel::class);

        return collect($this->iterateRequest($iterator))
                ->mapInto(Calendar::class)
                ->all();
    }

    /**
     * Itereate the request pages and get all the data
     *
     * @param \Iterator $iterator
     *
     * @return array
     */
    protected function iterateRequest($iterator)
    {
        try {
            return Api::iterateCollectionRequest($iterator);
        } catch (IdentityProviderException $e) {
            throw new ConnectionErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
