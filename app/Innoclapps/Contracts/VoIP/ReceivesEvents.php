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

namespace App\Innoclapps\Contracts\VoIP;

use Illuminate\Http\Request;

interface ReceivesEvents
{
    /**
     * Set the call events URL
     *
     * @param string $url The URL the client events webhook should be pointed to
     *
     * @return static
     */
    public function setEventsUrl(string $url) : static;

    /**
     * Handle the VoIP service events request
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function events(Request $request);
}
