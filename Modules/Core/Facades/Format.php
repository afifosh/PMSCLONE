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
use Modules\Core\Date\Format as BaseFormat;

/**
 * @method static string format($value, $format, ?\Modules\Core\Contracts\Localizeable $user = null)
 * @method static string dateTime($value, ?\Modules\Core\Contracts\Localizeable $user = null)
 * @method static string date($value, ?\Modules\Core\Contracts\Localizeable $user = null)
 * @method static string time($value, ?\Modules\Core\Contracts\Localizeable $user = null)
 * @method static string separateDateAndTime(string $date, string $time, ?\Modules\Core\Contracts\Localizeable $user = null)
 * @method static string|null diffForHumans($value, ?\Modules\Core\Contracts\Localizeable $user = null)
 *
 * @mixin \Modules\Core\Date\Format
 */
class Format extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BaseFormat::class;
    }
}
