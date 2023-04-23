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

namespace App\Innoclapps\Settings;

class SettingsMenu
{
    /**
     * @var array
     */
    protected static array $items = [];

    /**
     * Register new settings menu item
     *
     * @param \App\Innoclapps\Settings\SettingsMenuItem $item
     * @param string $id
     *
     * @return void
     */
    public static function register(SettingsMenuItem $item, string $id) : void
    {
        static::$items[$id] = $item->setId($id);
    }

    /**
     * Find menu item by the given id
     *
     * @param string $id
     *
     * @return \App\Innoclapps\Settings\SettingsMenuItem|null
     */
    public static function find(string $id) : ?SettingsMenuItem
    {
        return collect(static::$items)->first(fn ($item) => $item->getId() === $id);
    }

    /**
     * Get all of the registered settings menu items
     *
     * @return array
     */
    public static function all() : array
    {
        return collect(static::$items)->sortBy('order')->values()->all();
    }
}
