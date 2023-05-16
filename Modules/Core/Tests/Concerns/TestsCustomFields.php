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

namespace Modules\Core\Tests\Concerns;

use Modules\Core\Facades\Fields;

trait TestsCustomFields
{
    protected function fieldsTypesThatRequiresDatabaseColumnCreation()
    {
        return Fields::customFieldable()->where('multioptionable', false)->keys()->all();
    }

    protected function fieldsTypesThatDoesntRequiresDatabaseColumnCreation()
    {
        return Fields::customFieldable()->where('multioptionable', true)->keys()->all();
    }
}
