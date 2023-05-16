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
use Modules\Core\Menu\MenuManager;

/**
 * @method static static register(\Modules\Core\Menu\MenuItem|array $items)
 * @method static static registerItem(\Modules\Core\Menu\MenuItem $item)
 * @method static \Illuminate\Support\Collection get()
 * @method static static clear()
 *
 * @mixin \Modules\Core\Menu\MenuManager
 */
class Menu extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MenuManager::class;
    }
}
