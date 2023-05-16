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

namespace Modules\Core\Fields;

use Illuminate\Support\Collection;
use Modules\Core\Casts\ISO8601Date;
use Modules\Core\Casts\ISO8601DateTime;

class CustomFieldResourceCollection extends Collection
{
    /**
     * Fillable attributes cache cache
     */
    protected ?array $fillable = null;

    /**
     * Castable fields cache
     */
    protected ?self $castable = null;

    /**
     * Model casts cache
     */
    protected ?array $modelCasts = null;

    /**
     * Get the optionable fields
     *
     * @return static
     */
    public function optionable()
    {
        return $this->filter->isOptionable();
    }

    /**
     * Get the fillable attributes for the model
     *
     * @return array
     */
    public function fillable()
    {
        if (is_null($this->fillable)) {
            $this->fillable = $this->filter->isNotMultiOptionable()->pluck('field_id')->all();
        }

        return $this->fillable;
    }

    /**
     * Get the model casts
     *
     * @return array
     */
    public function modelCasts()
    {
        if (is_null($this->modelCasts)) {
            $data = $this->castableFieldsData();

            $this->modelCasts = $this->castable()->mapWithKeys(function ($field) use ($data) {
                return [$field->field_id => $data[$field->field_type]];
            })->all();
        }

        return $this->modelCasts;
    }

    /**
     * Get the castable fields
     *
     * @return static
     */
    public function castable()
    {
        if (is_null($this->castable)) {
            $this->castable = $this->whereIn('field_type', array_keys($this->castableFieldsData()));
        }

        return $this->castable;
    }

    /**
     * Get the castable fields data
     *
     * @return array
     */
    protected function castableFieldsData()
    {
        return [
            'Text' => 'string',
            'Textarea' => 'string',
            'Email' => 'string',
            'Timezone' => 'string',
            'Date' => ISO8601Date::class,
            'DateTime' => ISO8601DateTime::class,
            'Boolean' => 'boolean',
            'Numeric' => 'decimal:3',
            'Number' => 'int',
            'Radio' => 'int',
            'Select' => 'int',
        ];
    }
}
