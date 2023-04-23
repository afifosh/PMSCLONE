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

namespace App\Innoclapps\Fields;

use App\Innoclapps\Contracts\Fields\Customfieldable;
use App\Innoclapps\Contracts\Fields\CustomfieldUniqueable;

class Text extends Field implements Customfieldable, CustomfieldUniqueable
{
    /**
     * This field support input group
     */
    public bool $supportsInputGroup = true;

    /**
     * Input type
     */
    public string $inputType = 'text';

    /**
     * Field component
     */
    public ?string $component = 'text-field';

    /**
     * Specify type attribute for the text field
     */
    public function inputType(string $type) : static
    {
        $this->inputType = $type;

        return $this;
    }

    /**
     * Create the custom field value column in database
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     * @param string $fieldId
     *
     * @return void
     */
    public static function createValueColumn($table, $fieldId)
    {
        $table->string($fieldId)->nullable();
    }

    /**
     * jsonSerialize
     */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
            'inputType' => $this->inputType,
        ]);
    }
}
