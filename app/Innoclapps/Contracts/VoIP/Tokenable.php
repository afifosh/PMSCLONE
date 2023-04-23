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

interface Tokenable
{
    /**
     * Create new client token for the logged in user
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function newToken(Request $request);
}
