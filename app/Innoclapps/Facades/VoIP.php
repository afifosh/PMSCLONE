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

use Illuminate\Support\Facades\Facade;
use App\Innoclapps\Contracts\VoIP\VoIPClient;

class VoIP extends Facade
{
    /**
     * Get the events URL
     *
     * @return string
     */
    public static function eventsUrl()
    {
        // Uses config('app.url') because of NGROK, for testing purposes
        url()->forceRootUrl(config('app.url'));

        return tap(url()->route(config('innoclapps.voip.endpoints.events')), function () {
            url()->forceRootUrl(null);
        });
    }

    /**
     * Get the new call URL
     *
     * @return string
     */
    public static function callUrl()
    {
        // Uses config('app.url') because of NGROK, for testing purposes
        url()->forceRootUrl(config('app.url'));

        return tap(url()->route(config('innoclapps.voip.endpoints.call')), function () {
            url()->forceRootUrl(null);
        });
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return VoIPClient::class;
    }
}
