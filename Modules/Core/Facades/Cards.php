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
use Modules\Core\Card\CardsManager;

/**
 * @method static static register(string $resourceName, mixed $provider)
 * @method static \Illuminate\Support\Collection resolve(string $resourceName)
 * @method static \Illuminate\Support\Collection forResource(string $resourceName)
 * @method static \Illuminate\Support\Collection resolveForDashboard()
 * @method static \Illuminate\Support\Collection registered()
 *
 * @mixin \Modules\Core\Card\CardsManager
 */
class Cards extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return CardsManager::class;
    }
}
