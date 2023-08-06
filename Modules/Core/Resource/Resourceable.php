<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\Core\Resource;

use Closure;
use Modules\Core\Facades\Innoclapps;

/** @mixin \Modules\Core\Models\Model */
trait Resourceable
{
    public static array $beforeSyncCustomFieldOptions = [];

    public static array $afterSyncCustomFieldOptions = [];

    /**
     * Boot the resource model
     */
    protected static function bootResourceable(): void
    {
        static::bootFieldsEvents();
    }

    /**
     * Register event for when before sync custom field options
     */
    public static function beforeSyncCustomFieldOptions(Closure $callback)
    {
        static::$beforeSyncCustomFieldOptions[static::class][spl_object_hash($callback)] = $callback;
    }

    /**
     * Register event for when after sync custom field options
     */
    public static function afterSyncCustomFieldOptions(Closure $callback)
    {
        static::$afterSyncCustomFieldOptions[static::class][spl_object_hash($callback)] = $callback;
    }

    /**
     * A model can have many associated resources
     */
    public function associatedResources(): array
    {
        $associations = [];

        foreach (static::resource()->availableAssociations() as $resource) {
            $associations[$resource->name()] = $this->{$resource->associateableName()};
        }

        return $associations;
    }

    /**
     * Check whether all the model associations are loaded
     */
    public function associationsLoaded(): bool
    {
        $associateables = static::resource()->availableAssociations();
        $totalAssociateables = count($associateables);

        $totalLoaded = $associateables->reduce(function ($carry, $resource) {
            return $this->relationLoaded($resource->associateableName()) ? ($carry + 1) : $carry;
        }, 0);

        return $totalAssociateables > 0 && $totalAssociateables === $totalLoaded;
    }

    /**
     * Get the model related resource instance
     *
     * @return \Modules\Core\Resource\Resource
     */
    public static function resource()
    {
        return Innoclapps::resourceByModel(static::class);
    }

    /**
     * Fill the model with an array of attributes.
     *
     * @return static
     *
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes)
    {
        return parent::fill($attributes);
    }

    /**
     * Get the casts array.
     */
    public function getCasts(): array
    {
        return array_merge(parent::getCasts(), static::getCustomFields()->modelCasts());
    }

    /**
     * Boot the trait and register the necessary events
     */
    protected static function bootCustomFields(): void
    {
        static::bootCustomFieldsWithOptions();
    }

    /**
     * Boot the custom fields with options
     */
    protected static function bootCustomFieldsWithOptions(): void
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
     * Get the custom fields service instance.
     */
    protected static function getCustomFieldService(): CustomFieldService
    {
        return once(function () {
            return new CustomFieldService();
        });
    }

    /**
     * Get the model custom fields.
     */
    public static function getCustomFields(): CustomFieldResourceCollection
    {
        if (! static::resource()) {
            return new CustomFieldResourceCollection([]);
        }

        return static::getCustomFieldService()->forResource(
            static::resource()->name()
        );
    }

    /**
     * If the key exists in relations then
     * return call to relation or else
     * return the call to the parent
     *
     * @todo  in future use https://laravel.com/docs/10.x/eloquent-relationships#dynamic-relationships
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (! is_null(static::getCustomFieldByRelationship($name))) {
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
     * @todo  in future use https://laravel.com/docs/10.x/eloquent-relationships#dynamic-relationships
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (static::resource() && $field = static::getCustomFieldByRelationship($name)) {
            if (! $field->isMultiOptionable()) {
                return $this->newSingleValueOptionCustomFieldRelation($field);
            }

            return $this->newMultiValueOptionCustomFieldRelation($field);
        }

        return parent::__call($name, $arguments);
    }
}
