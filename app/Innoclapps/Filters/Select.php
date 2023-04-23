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

namespace App\Innoclapps\Filters;

class Select extends Optionable
{
    /**
     * Defines a filter type
     *
     * @return string
     */
    public function type() : string
    {
        return 'select';
    }
}
