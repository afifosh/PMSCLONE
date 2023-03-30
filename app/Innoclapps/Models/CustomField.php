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

use Illuminate\Support\Str;
use App\Innoclapps\Fields\Field;
use Illuminate\Support\Collection;
use App\Innoclapps\Fields\CustomFieldFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CustomField extends Model
{
    /**
     * @var \App\Innoclapps\Fields\Field
     */
    protected $instance;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_type', 'field_id', 'resource_name', 'label', 'is_unique',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_unique' => 'boolean',
    ];

    /**
     * A custom field has many options
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(CustomFieldOption::class)->orderBy('display_order');
    }

    /**
     * Get the optionable custom field model relation name
     *
     * https://laravel.com/docs/7.x/eloquent-relationships#defining-relationships
     * "Relationship names cannot collide with attribute names as that could lead to your model not being able to know which one to resolve."
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function relationName() : Attribute
    {
        return Attribute::get(fn () => 'customField' . Str::studly($this->field_id));
    }

    /**
     * Get the instance from the field class
     *
     * @return \App\Innoclapps\Fields\Field
     */
    public function instance()
    {
        if (! $this->instance) {
            $this->instance = CustomFieldFactory::createInstance($this);
        }

        return $this->instance;
    }

    /**
     * Check whether the custom field is multi optionable
     *
     * @return boolean
     */
    public function isMultiOptionable() : bool
    {
        return $this->instance()->isMultiOptionable();
    }

    /**
     * Check whether the custom field is not multi optionable
     *
     * @return boolean
     */
    public function isNotMultiOptionable() : bool
    {
        return ! $this->isMultiOptionable();
    }

    /**
     * Check whether the custom field is optionable
     *
     * @return boolean
     */
    public function isOptionable() : bool
    {
        return $this->instance()->isOptionable();
    }

    /**
     * Check whether the custom field is not optionable
     *
     * @return boolean
     */
    public function isNotOptionable() : bool
    {
        return ! $this->isOptionable();
    }

    /**
     * Prepate the selected options for front-end
     *
     * @param \Illuminate\Database\Eloquent\Model $related
     *
     * @return array
     */
    public function prepareRelatedOptions($related) : array
    {
        return $this->prepareOptions($related->{$this->relationName});
    }

    /**
     * Prepare the options for front-end
     *
     * @param \Illuminate\Support\Collection|null $options
     *
     * @return array
     */
    public function prepareOptions(?Collection $options = null) : array
    {
        return ($options ?? $this->options)->map(function ($option) {
            return [
                'id'           => $option->id,
                'label'        => $option->name,
                'swatch_color' => $option->swatch_color,
            ];
        })->all();
    }
}
