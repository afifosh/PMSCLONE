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

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Innoclapps\Fields\MorphMany;
use App\Innoclapps\Contracts\Resources\ResourcefulRequestHandler;
use App\Innoclapps\Contracts\Fields\TracksMorphManyModelAttributes;
use App\Innoclapps\Contracts\Fields\HandlesChangedMorphManyAttributes;

class ResourcefulHandlerWithFields extends ResourcefulHandler implements ResourcefulRequestHandler
{
    /**
     * Handle the resource store action
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function store()
    {
        [$attributes, $callbacks] = $this->getAttributes();

        $record = $this->handleAssociatedResources(
            $this->repository->create($attributes)
        );

        $record::withoutTouching(function () use ($record, $attributes) {
            foreach ($this->morphManyFields() as $relation => $values) {
                foreach ($values ?? [] as $attributes) {
                    $record->{$relation}()->create($attributes);
                }
            }
        });

        $callbacks->each->__invoke($record);

        return $record;
    }

    /**
     * Handle the resource update action
     *
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function update($id)
    {
        [$attributes, $callbacks] = $this->getAttributes();

        $this->withSoftDeleteCriteria($this->repository);

        $record = $this->handleAssociatedResources(
            $this->repository->update($attributes, $id)
        );

        $this->syncMorphManyFields($record);

        $callbacks->each->__invoke($record);

        return $record;
    }

    /**
     * Handle the resource update action
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateUsingModel($model)
    {
        [$attributes, $callbacks] = $this->getAttributes();

        $record = $this->handleAssociatedResources(
            $this->repository->updateUsingModel($attributes, $model)
        );

        $this->syncMorphManyFields($record);

        $callbacks->each->__invoke($record);

        return $record;
    }

    /**
     * Get the morph many fields
     *
     * @return \App\Innoclapps\Fields\FieldsCollection
     */
    protected function morphManyFields()
    {
        return $this->request->authorizedFields()
            ->whereInstanceOf(MorphMany::class)
            ->reject(function ($field) {
                return $this->request->missing($field->requestAttribute());
            })
            ->mapWithKeys(function ($field) {
                return $field->storageAttributes($this->request, $field->requestAttribute());
            });
    }

    /**
     * Get the attributes for storage
     *
     * @return array
     */
    protected function getAttributes()
    {
        $parsed = $this->parseAttributes();

        $attributes = $parsed->reject(fn ($data) => is_callable($data['attributes']))
            ->mapWithKeys(function ($data, $attribute) {
                return $data['field'] ? $data['attributes'] : [$attribute => $data['value']];
            })->all();

        $callables = $parsed->filter(function ($data) {
            return is_callable($data['attributes']);
        })
        ->map(function ($data) {
            return $data['attributes'];
        });

        return [$attributes, $callables];
    }

    /**
     * Get the attributes for the request
     *
     * @return \Illuminate\Support\Collection
     */
    protected function parseAttributes()
    {
        return collect($this->request->all())->mapWithKeys(function ($value, $attribute) {
            $field = $this->request->authorizedFields()->findByRequestAttribute($attribute);

            $attributes = $field ? $field->storageAttributes($this->request, $field->requestAttribute()) : null;

            return [
                $attribute => [
                    'field'      => $field,
                    'value'      => $value,
                    'attributes' => $attributes,
                ],
            ];
        });
    }

    /**
     * Sync the MorphMany fields
     *
     * @param \Illuminate\Database\Eloquent\Model $record
     *
     * @return void
     */
    protected function syncMorphManyFields($record)
    {
        foreach ($this->morphManyFields() as $relation => $values) {
            $beforeUpdateAttributes = [];
            $afterUpdateAttributes  = [];

            $tracksChanges   = $record->{$relation}()->getModel() instanceof TracksMorphManyModelAttributes;
            $trackAttributes = $tracksChanges ? (array) $record->{$relation}()->getModel()->trackAttributes() : [];

            if (! $record->relationLoaded($relation)) {
                $record->load([$relation, $relation . '.' . Str::before($record->{$relation}()->getMorphType(), '_type')]);
            }

            foreach (($trackAttributes ? $record->{$relation} : []) as $morphMany) {
                $beforeUpdateAttributes[] = $morphMany->only($trackAttributes);
            }

            $this->syncMorphManyField((array) $values, $relation, $record);

            foreach (($trackAttributes ? $record->{$relation} : []) as $morphMany) {
                $afterUpdateAttributes[] = $morphMany->only($trackAttributes);
            }

            if ($record instanceof HandlesChangedMorphManyAttributes && $beforeUpdateAttributes != $afterUpdateAttributes) {
                $record->morphManyAtributesUpdated($relation, $afterUpdateAttributes, $beforeUpdateAttributes);
            }
        }
    }

    /**
     * Sync the morph many field
     *
     * @param array $values
     * @param string $relation
     * @param Model $record
     *
     * @return void
     */
    protected function syncMorphManyField($values, $relation, $record)
    {
        foreach ($values as $attributes) {
            $delete   = isset($attributes['_delete']);
            $fillable = Arr::except($attributes, ['_delete', '_track_by']);

            if ($delete) {
                $record->{$relation}->find($attributes['id'])->delete();
                $record->setRelation($relation, $record->{$relation}->except($attributes['id']));
            } elseif (isset($attributes['id'])) {
                tap($record->{$relation}->find($attributes['id'])->fill($fillable))->save();
            } else {
                $trackBy = $attributes['_track_by'] ?? $fillable;
                $model   = $record->{$relation}->first(function ($item) use ($trackBy) {
                    foreach ($trackBy as $key => $value) {
                        if ($item[$key] === $value) {
                            return true;
                        }
                    }
                });

                if ($model) {
                    $model->fill($fillable)->save();
                } else {
                    $model = $record->{$relation}()->create($fillable);
                    $record->setRelation($relation, $record->{$relation}->push($model));
                }
            }
        }
    }
}
