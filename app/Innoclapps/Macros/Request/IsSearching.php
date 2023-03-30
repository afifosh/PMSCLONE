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

class IsSearching
{
    /**
     * Determine whether user is performing search via the repository RequestCriteria
     *
     * @return boolean
     */
    public function __invoke()
    {
        return ! is_null(request()->get('q', null));
    }
}
