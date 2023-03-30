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

namespace App\Innoclapps\Macros\Request;

class IsZapier
{
    /**
     * Determine whether current request is from Zapier
     *
     * @return boolean
     */
    public function __invoke()
    {
        return request()->header('user-agent') === 'Zapier';
    }
}
