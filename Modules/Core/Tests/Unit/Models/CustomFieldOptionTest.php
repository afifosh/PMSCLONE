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

namespace Modules\Core\Tests\Unit\Models;

use Modules\Core\Models\CustomField;
use Modules\Core\Models\CustomFieldOption;
use Tests\TestCase;

class CustomFieldOptionTest extends TestCase
{
    public function test_custom_field_option_has_field()
    {
        $field = $this->makeField();
        $field->save();

        $option = new CustomFieldOption(['name' => 'Option 1', 'display_order' => 1]);
        $field->options()->save($option);

        $this->assertInstanceof(CustomField::class, $option->field);
    }

    protected function makeField($attrs = [])
    {
        return new CustomField(array_merge([
            'field_id' => 'field_id',
            'field_type' => 'Text',
            'resource_name' => 'resource',
            'label' => 'Label',
        ], $attrs));
    }
}
