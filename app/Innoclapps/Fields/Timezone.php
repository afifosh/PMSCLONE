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

use Illuminate\Support\Arr;
use App\Innoclapps\Facades\Timezone as Facade;
use App\Innoclapps\Rules\ValidTimezoneCheckRule;
use App\Innoclapps\Contracts\Fields\Customfieldable;

class Timezone extends Field implements Customfieldable
{
    /**
     * Field component
     */
    public ?string $component = 'timezone-field';

    /**
     * Initialize Timezone field
     *
     * @param string $attribute
     * @param string|null $label
     */
    public function __construct($attribute, $label = null)
    {
        parent::__construct($attribute, $label ?? __('app.timezone'));

        $this->rules(new ValidTimezoneCheckRule)
            ->provideSampleValueUsing(fn () => Arr::random(tz()->all()));
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
      * Provide the options intended for Zapier
      *
      * @return array
      */
    public function jsonSerialize() : array
    {
        return array_merge(parent::jsonSerialize(), [
              'timezones' => Facade::toArray(),
          ]);
    }
}
