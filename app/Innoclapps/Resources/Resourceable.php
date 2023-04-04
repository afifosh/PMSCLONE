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

namespace App\Innoclapps\Resources;

use Closure;
use App\Innoclapps\Facades\Zapier;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Models\CustomFieldOption;
use App\Innoclapps\Contracts\Resources\AcceptsCustomFields;
use App\Innoclapps\Contracts\Repositories\CustomFieldRepository;

trait Resourceable
{
    /**
     * @var array
     */
    public static array $beforeSyncCustomFieldOptions = [];

    /**
     * @var array
     */
    public static array $afterSyncCustomFieldOptions = [];

    /**
     * Boot the resource model
     *
     * @return void
     */
    protected static function bootResourceable()
    {
            static::bootFieldsEvents();

            if (static::resource() instanceof AcceptsCustomFields) {
                static::bootCustomFields();
            }

    }

    /**
     * Register event for when before sync custom field options
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function beforeSyncCustomFieldOptions(Closure $callback)
    {
        static::$beforeSyncCustomFieldOptions[static::class][spl_object_hash($callback)] = $callback;
    }

    /**
     * Register event for when after sync custom field options
     *
     * @param \Closure $callback
     *
     * @return void
     */
    public static function afterSyncCustomFieldOptions(Closure $callback)
    {
        static::$afterSyncCustomFieldOptions[static::class][spl_object_hash($callback)] = $callback;
    }

    /**
     * A model can have many associated resources
     *
     * @return array
     */
    public function associatedResources()
    {
        $associations = [];

        foreach (static::resource()->availableAssociations() as $resource) {
            $associations[$resource->name()] = $this->{$resource->associateableName()};
        }

        return $associations;
    }

    /**
     * Check whether all the model associations are loaded
     *
     * @return boolean
     */
    public function associationsLoaded()
    {
        $associateables      = static::resource()->availableAssociations();
        $totalAssociateables = count($associateables);

        $totalLoaded = $associateables->reduce(function ($carry, $resource) {
            return $this->relationLoaded($resource->associateableName()) ? ($carry + 1) : $carry;
        }, 0);

        return $totalAssociateables > 0 && $totalAssociateables === $totalLoaded;
    }

    /**
     * Get the model related resource instance
     *
     * @return \App\Innoclapps\Resources\Resource
     */
    public static function resource()
    {
        return Innoclapps::resourceByModel(static::class);
    }


    /**
     * Boot the fields model events
     *
     * @return void
     */
    protected static function bootFieldsEvents()
    {
        // Available events from the Field trait
        $events = ['creating', 'created', 'updating', 'updated', 'deleting', 'deleted'];

        foreach ($events as $event) {
            static::{$event}(function ($model) use ($event) {
                $fields = static::resource()->resolveFields();

                $fields->each(function ($field) use ($model, $event) {
                    $method = 'record' . ucfirst($event);

                    $field->{$method}($model);
                });
            });
        }
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @param array $attributes
     * @return static
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        // Because model may be initialized without attributes in this case
        // first, we will check if there are attributes, then will merge the non-relation
        // custom field field_id's as fillable attributes
        if (static::resource() instanceof AcceptsCustomFields &&
                count($attributes) > 0 &&
                ! static::isUnguarded() &&
                count($this->getFillable()) > 0) {
            $this->fillable(array_unique(
                array_merge(
                    $this->getFillable(),
                    static::getCustomFields()->fillable()
                )
            ));
        }

        return parent::fill($attributes);
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return array_merge(parent::getCasts(), static::getCustomFields()->modelCasts());
    }

    /**
     * Boot the trait and register the necessary events
     *
     * @return void
     */
    protected static function bootCustomFields()
    {
        static::bootCustomFieldsWithOptions();
    }

    /**
     * Boot the custom fields with options
     *
     * @return void
     */
    protected static function bootCustomFieldsWithOptions()
    {
        static::deleting(function ($model) {
            if (! $model->usesSoftDeletes() || $model->isForceDeleting()) {
                foreach (static::getCustomFields()->optionable()->filter->isMultiOptionable() as $field) {
                    $model->{$field->relationName}()->detach();
                }
            }
        });
    }

    /**
     * Get the custom fields repository
     *
     * @return \App\Innoclapps\Contracts\Repositories\CustomFieldRepository
     */
    protected static function getCustomFieldRepository()
    {
        // Use once, to avoid resolving the repository many times in the container
        return once(function () {
            return app(CustomFieldRepository::class);
        });
    }

    /**
     * Get the model custom fields
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCustomFields()
    {
        \Log::info('resources'.json_encode(static::resource()));
        return static::getCustomFieldRepository()->forResource(
            static::resource()->name()
        );
    }

    /**
     * Determine if a relation exists in dynamic relations list
     *
     * @param $name
     *
     * @return boolean
     */
    public static function hasCustomFieldRelation($name)
    {
        return ! is_null(static::getCustomFields()->optionable()->firstWhere('relationName', $name));
    }

    /**
     * Create new custom field multi value options relation
     *
     * @param \App\Innoclapps\Models\CustomField $field
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function newMultiValueOptionCustomFieldRelation($field)
    {
        $instance = $this->newRelatedInstance(CustomFieldOption::class);

        return $this->newMorphToMany(
            $instance->newQuery(),
            $this,
            'model',
            'model_has_custom_field_options',
            'model_id',
            'option_id',
            $this->getKeyName(),
            $instance->getKeyName(),
            $field->relationName,
            false
        )->wherePivot('custom_field_id', $field->id);
    }

    /**
     * Create new custom field single value options relation
     *
     * @param \App\Innoclapps\Models\CustomField $field
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    protected function newSingleValueOptionCustomFieldRelation($field)
    {
        $instance = $this->newRelatedInstance(CustomFieldOption::class);

        return $this->newBelongsTo(
            $instance->newQuery(),
            $this,
            $field->field_id,
            $instance->getKeyName(),
            $field->relationName
        );
    }

    /**
     * If the key exists in relations then
     * return call to relation or else
     * return the call to the parent
     *
     * @todo  in future use https://laravel.com/docs/8.x/eloquent-relationships#dynamic-relationships
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (static::hasCustomFieldRelation($name)) {
            if ($this->relationLoaded($name)) {
                return $this->relations[$name];
            }

            return $this->getRelationshipFromMethod($name);
        }

        return parent::__get($name);
    }

    /**
     * If the method exists in relations then
     * return the relation or else
     * return the call to the parent
     *
     * @todo  in future use https://laravel.com/docs/8.x/eloquent-relationships#dynamic-relationships
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (static::hasCustomFieldRelation($name)) {
            $field = static::getCustomFields()->optionable()->firstWhere('relationName', $name);

            if (! $field->isMultiOptionable()) {
                return $this->newSingleValueOptionCustomFieldRelation($field);
            }

            return $this->newMultiValueOptionCustomFieldRelation($field);
        }

        return parent::__call($name, $arguments);
    }
}
