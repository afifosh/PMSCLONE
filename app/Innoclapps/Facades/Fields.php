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

namespace App\Innoclapps\Facades;

use App\Innoclapps\Fields\Manager;
use Illuminate\Support\Facades\Facade;

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
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }
}
