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

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Modules\Core\Fields\CustomFieldFactory;
use Modules\Core\Fields\Field;

class CustomField extends Model
{
    /**
     * @var \Modules\Core\Fields\Field
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
     */
    public function options(): HasMany
    {
        return $this->hasMany(CustomFieldOption::class)->orderBy('display_order');
    }

    /**
     * Get the optionable custom field model relation name
     *
     * https://laravel.com/docs/7.x/eloquent-relationships#defining-relationships
     * "Relationship names cannot collide with attribute names as that could lead to your model not being able to know which one to resolve."
     */
    public function relationName(): Attribute
    {
        return Attribute::get(fn () => 'customField'.Str::studly($this->field_id));
    }

    /**
     * Get the instance from the field class
     */
    public function instance(): Field
    {
        if (! $this->instance) {
            $this->instance = CustomFieldFactory::createInstance($this);
        }

        return $this->instance;
    }

    /**
     * Check whether the custom field is multi optionable
     */
    public function isMultiOptionable(): bool
    {
        return $this->instance()->isMultiOptionable();
    }

    /**
     * Check whether the custom field is not multi optionable
     */
    public function isNotMultiOptionable(): bool
    {
        return ! $this->isMultiOptionable();
    }

    /**
     * Check whether the custom field is optionable
     */
    public function isOptionable(): bool
    {
        return $this->instance()->isOptionable();
    }

    /**
     * Check whether the custom field is not optionable
     */
    public function isNotOptionable(): bool
    {
        return ! $this->isOptionable();
    }

    /**
     * Prepate the selected options for front-end
     *
     * @param  \Illuminate\Database\Eloquent\Model  $related
     */
    public function prepareRelatedOptions($related): array
    {
        return $this->prepareOptions($related->{$this->relationName});
    }

    /**
     * Name attribute accessor
     *
     * Supports translation from language file
     */
    public function label(): Attribute
    {
        return Attribute::get(function (string $value, array $attributes) {
            $customKey = 'custom.custom_field.'.$attributes['field_id'];

            if (Lang::has($customKey)) {
                return __($customKey);
            } elseif (Lang::has($value)) {
                return __($value);
            }

            return $value;
        });
    }

    /**
     * Prepare the options for front-end
     */
    public function prepareOptions(?Collection $options = null): array
    {
        return ($options ?? $this->options)->map(function ($option) {
            return [
                'id' => $option->id,
                'label' => $option->name,
                'swatch_color' => $option->swatch_color,
            ];
        })->all();
    }
}
