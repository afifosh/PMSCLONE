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
use Modules\Core\Fields\FieldsManager;

/**
 * @method static static group(string $group, mixed $provider)
 * @method static static add(string $group, mixed $provider)
 * @method static static replace(string $group, mixed $provider)
 * @method static \Modules\Core\Fields\FieldsCollection resolve(string $group, string $view)
 * @method static \Modules\Core\Fields\FieldsCollection resolveForDisplay(string $group, string $view)
 * @method static \Modules\Core\Fields\FieldsCollection resolveCreateFieldsForDisplay(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveUpdateFieldsForDisplay(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveDetailFieldsForDisplay(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveCreateFields(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveUpdateFields(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveDetailFields(string $group)
 * @method static \Modules\Core\Fields\FieldsCollection resolveAndAuthorize(string $group, ?string $view = null)
 * @method static \Modules\Core\Fields\FieldsCollection resolveForSettings(string $group, string $view)
 * @method static \Modules\Core\Fields\FieldsCollection inGroup(string $group, ?string $view = null)
 * @method static void customize(mixed $data, string $group, string $view)
 * @method static array customized(string $group, string $view, ?string $attribute = null)
 * @method static void flushLoadedCache()
 * @method static void flushRegisteredCache()
 * @method static \Illuminate\Support\Collection customFieldable()
 * @method static array getOptionableCustomFieldsTypes()
 * @method static array getNonOptionableCustomFieldsTypes()
 * @method static array customFieldsTypes()
 * @method static \Illuminate\Support\Collection getCustomFieldsForResource(string $resourceName)
 *
 * @mixin \Modules\Core\Fields\FieldsManager
 */
class Fields extends Facade
{
    /**
     * The create view name
     */
    const CREATE_VIEW = 'create';

    /**
     * The update view name
     */
    const UPDATE_VIEW = 'update';

    /**
     * The detail view name
     */
    const DETAIL_VIEW = 'detail';

    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return FieldsManager::class;
    }
}
