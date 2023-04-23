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

namespace App\Innoclapps\Contracts;

use Illuminate\Database\Eloquent\Casts\Attribute;

interface Presentable
{
    public function displayName() : Attribute;

    public function path() : Attribute;

    public function getKeyName();

    public function getKey();
}
