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

namespace App\Innoclapps;

trait Makeable
{
    /**
     * Create new instance
     *
     * @param array $params
     *
     * @return static
     */
    public static function make(...$params) : static
    {
        return new static(...$params);
    }
}
