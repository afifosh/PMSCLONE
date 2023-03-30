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

namespace App\Innoclapps\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    /**
     * Permissions guard name
     *
     * @var string
     */
    public $guard_name = 'api';
}
