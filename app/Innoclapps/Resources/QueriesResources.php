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

use App\Innoclapps\Fields\HasMany;
use App\Innoclapps\Fields\MorphMany;
use App\Innoclapps\Facades\Innoclapps;
use Illuminate\Database\Eloquent\Model;
use App\Innoclapps\Models\PinnedTimelineSubject;
use App\Innoclapps\Contracts\Fields\Customfieldable;
use App\Innoclapps\Criteria\WithPinnedTimelineSubjectsCriteria;

trait QueriesResources
{
    /**
     * Prepare global search query
     *
     * @param int $limit
     * @param null|\App\Innoclapps\Repositories\AppRepository $repository
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function globalSearchQuery(int $limit, $repository = null)
    {
        $repository ??= static::repository();

        if ($criteria = $this->viewAuthorizedRecordsCriteria()) {
            $repository->pushCriteria($criteria);
        }

        return $repository->limit($limit);
    }

    /**
     * Prepare display query
     *
     * @param null|\App\Innoclapps\Repositories\AppRepository $repository
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function displayQuery($repository = null)
    {
        $fields = $this->resolveFields();
        $repository ??= static::repository();

        [$with, $withCount] = static::getEagerloadableRelations($fields);

        $with = $with->merge($repository->getResponseRelations());

        return $repository->withCount($withCount->all())->with($with->all());
    }

    /**
     * Prepare index query
     *
     * @param null|\App\Innoclapps\Repositories\AppRepository $repository
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function indexQuery($repository = null)
    {
        $repository ??= static::repository();

        [$with, $withCount] = static::getEagerloadableRelations($this->fieldsForIndexQuery());

        if ($criteria = $this->viewAuthorizedRecordsCriteria()) {
            $repository->pushCriteria($criteria);
        }

        return $repository->withCount($withCount->all())->with($with->all());
    }

    /**
     * Get the fields when creating index query
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fieldsForIndexQuery()
    {
        return $this->resolveFields()->reject(function ($field) {
            return $field instanceof HasMany;
        });
    }

    /**
     * Create the query when the resource is associated and the data is intended for the timeline
     *
     * @param \App\Innoclapps\Models\Model $subject
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function timelineQuery($subject)
    {
        $repository = $this->associatedIndexQuery($subject, false)
            ->with('pinnedTimelineSubjects')
            ->pushCriteria(new WithPinnedTimelineSubjectsCriteria($subject))
            ->orderBy((new PinnedTimelineSubject)->getQualifiedCreatedAtColumn(), 'desc');

        if ($repository->getModel()->usesTimestamps()) {
            $repository->orderBy($repository->getModel()->getQualifiedCreatedAtColumn(), 'desc');
        }

        return $repository;
    }

    /**
     * Create query when the resource is associated for index
     *
     * @param \App\Innoclapps\Models\Model $primary
     * @param bool $applyOrder
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function associatedIndexQuery($primary, $applyOrder = true)
    {
        $repository                = static::repository();
        $model                     = $repository->getModel();
        $associateabelRelationName = Innoclapps::resourceByModel($primary)->associateableName();

        return tap($repository->columns(static::prefixColumns())
            ->with($this->withWhenAssociated())
            ->withCount($this->withCountWhenAssociated())
            ->whereHas($associateabelRelationName, function ($query) use ($primary) {
                return $query->where($primary->getKeyName(), $primary->getKey());
            }), function ($instance) use ($model, $applyOrder) {
                if ($applyOrder && $model->usesTimestamps()) {
                    $instance->orderBy($model->getQualifiedCreatedAtColumn(), 'desc');
                }
            });
    }

    /**
     * Get the relations to eager load when quering associated records
     */
    public function withWhenAssociated() : array
    {
        return [];
    }

    /**
     * Get the countable relations when quering associated records
     */
    public function withCountWhenAssociated() : array
    {
        return [];
    }

    /**
     * Get the eager loadable relations from the given fields
     */
    public static function getEagerloadableRelations($fields) : array
    {
        $with    = $fields->pluck('belongsToRelation');
        $hasMany = $fields->whereInstanceOf(HasMany::class)->reject(function ($field) {
            return $field->excludeFromZapierResponse && request()->isZapier();
        });
        $morphMany = $fields->whereInstanceOf(MorphMany::class)->reject(function ($field) {
            return $field->excludeFromZapierResponse && request()->isZapier();
        });
        $customFieldAble = $fields->whereInstanceOf(Customfieldable::class);

        $with = $with->merge($hasMany->filter(function ($field) {
            return $field->count === false;
        })->pluck('hasManyRelationship'))->merge($morphMany->filter(function ($field) {
            return $field->count === false;
        })->pluck('morphManyRelationship'))
            ->merge($customFieldAble->filter(function ($field) {
                return $field->isCustomField() && $field->isOptionable();
            })->pluck('customField.relationName'));

        $withCount = $hasMany->push(...$morphMany->all())->filter(function ($field) {
            return $field->count === true;
        })->map(function ($field) {
            $relationName = $field instanceof HasMany ? 'hasManyRelationship' : 'morphManyRelationship';

            return $field->{$relationName} . ' as ' . $field->countKey();
        });

        return array_map(function ($collection) {
            return $collection->filter()->unique();
        }, [$with, $withCount]);
    }

    /**
     * Apply the order from the resource to the given repository
     *
     * @param \App\Innoclapps\Repositories\AppRepository $repository
     *
     * @return \App\Innoclapps\Repositories\AppRepository
     */
    public function applyOrder($repository)
    {
        return $repository->orderBy(static::$orderBy, static::$orderByDir);
    }

    /**
     * Prefix the database table columns for the given resource
     *
     * @param \Illuminate\Database\Eloquent\Model|string|null $model
     */
    public static function prefixColumns($model = null) : array
    {
        if ($model instanceof Model) {
            $table = $model->getTable();
        } elseif (is_string($model)) {
            $table = $model;
        } else {
            $table = Innoclapps::resourceByName(
                static::name()
            )->repository()->getModel()->getTable();
        }

        return collect(\Schema::getColumnListing($table))
            ->map(function ($column) use ($table) {
                return $table . '.' . $column;
            })->all();
    }
}
