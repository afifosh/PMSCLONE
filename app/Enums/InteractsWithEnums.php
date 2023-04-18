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

namespace App\Enums;

trait InteractsWithEnums
{
    /**
     * Find enum by given name
     *
     * @param string $name
     *
     * @return static|null
     */
    public static function find(string $name) : ?self
    {
        return array_values(array_filter(static::cases(), function ($status) use ($name) {
            return $status->name == $name;
        }))[0] ?? null;
    }

    /**
     * Get a random enum instance
     *
     * @return self
     */
    public static function random() : self
    {
        return static::find(static::names()[array_rand(static::names())]);
    }

    /**
     * Get all the enum names
     *
     * @return array
     */
    public static function names() : array
    {
        return array_column(static::cases(), 'name');
    }
}
