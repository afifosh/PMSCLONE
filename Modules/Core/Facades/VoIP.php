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

namespace Modules\Core\Facades;

use Illuminate\Support\Facades\Facade;
use Modules\Core\Contracts\VoIP\VoIPClient;

/**
 * @mixin \Modules\Core\Contracts\VoIP\VoIPClient
 */
class VoIP extends Facade
{
    /**
     * Get the events URL
     *
     * @return string
     */
    public static function eventsUrl()
    {
        /** @var \Illuminate\Routing\UrlGenerator */
        $url = url();

        // Uses config('app.url') because of NGROK, for testing purposes
        $url->forceRootUrl(config('app.url'));

        return tap($url->route(config('core.voip.endpoints.events')), function () use ($url) {
            $url->forceRootUrl(null);
        });
    }

    /**
     * Get the new call URL
     *
     * @return string
     */
    public static function callUrl()
    {
        /** @var \Illuminate\Routing\UrlGenerator */
        $url = url();

        // Uses config('app.url') because of NGROK, for testing purposes
        $url->forceRootUrl(config('app.url'));

        return tap($url->route(config('core.voip.endpoints.call')), function () use ($url) {
            $url->forceRootUrl(null);
        });
    }

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return VoIPClient::class;
    }
}
