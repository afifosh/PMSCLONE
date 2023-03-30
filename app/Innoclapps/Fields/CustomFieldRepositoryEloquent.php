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
use App\Innoclapps\Facades\Fields;
use Illuminate\Support\Facades\DB;
use App\Innoclapps\Facades\Innoclapps;
use App\Innoclapps\Models\CustomField;
use Illuminate\Support\Facades\Schema;
use App\Innoclapps\QueryBuilder\Parser;
use Illuminate\Database\Schema\Blueprint;
use App\Innoclapps\Repository\AppRepository;
use App\Innoclapps\Criteria\WithTrashedCriteria;
use App\Innoclapps\Contracts\Fields\Customfieldable;
use App\Innoclapps\Contracts\Repositories\FilterRepository;
use App\Innoclapps\Contracts\Repositories\CustomFieldRepository;

class CustomFieldRepositoryEloquent extends AppRepository implements CustomFieldRepository
{
    /**
     * Custom fields cache
     *
     * @var array
     */
    protected static $cache;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public static function model()
    {
        return CustomField::class;
    }

    /**
     * Create new custom field in storage
     *
     * @param array $attributes
     *
     * @return \App\Innoclapps\Models\CustomField
     */
    public function create(array $attributes)
    {
        $resource = Innoclapps::resourceByName($attributes['resource_name']);

        $this->createColumn(
            app($resource::$model),
            $attributes['field_id'],
            $attributes['field_type'],
            $unique = array_key_exists('is_unique', $attributes) ? $attributes['is_unique'] : null,
        );

        // For seeders, do not pass the options array to the create method
        // As in Seeder, mass assignment protection is disabled
        $field = parent::create([
            'resource_name' => $attributes['resource_name'],
            'label'         => $attributes['label'],
            'field_type'    => $attributes['field_type'],
            'field_id'      => $attributes['field_id'],
            'is_unique'     => $unique,
        ]);

        if ($field->isOptionable()) {
            return $this->createOptions($attributes['options'], $field)->load('options');
        }

        $this->flushCache();

        return $field;
    }

    /**
     * Create options for the given field
     *
     * @param array $options
     * @param int|\App\Innoclapps\Models\CustomField $field
     *
     * @return \App\Innoclapps\Models\CustomField
     */
    public function createOptions($options, $field)
    {
        $field   = is_int($field) ? $this->find($field) : $field;
        $options = isset($options[0]) ? $options : [$options];

        $this->prepareOptionsForInsert($options)
            ->each(function ($option, $index) use ($field) {
                $field->options()->create([
                    'name'          => $option['name'],
                    'display_order' => $option['display_order'] ?? $index + 1,
                    'swatch_color'  => $option['swatch_color'] ?? null,
                ]);
            });

        return $field;
    }

    /**
     * Update the field in storage
     *
     * @param array $attributes
     * @param int $id
     *
     * @return \App\Innoclapps\Models\CustomField
     */
    public function update(array $attributes, $id)
    {
        $field = parent::update($attributes, $id);

        if ($field->isOptionable()) {
            $this->handleFieldOptionsUpdate($field, $attributes['options']);
        }

        $this->flushCache();

        return $field->load('options');
    }

    /**
     * Boot the repository
     *
     * @return void
     */
    public static function boot()
    {
        static::deleting(function ($model, $repository) {
            $repository->deleteColumn($model);
        });

        static::deleted(function ($model, $repository) {
            $repository->handleDeletedFieldFiltersRules($model);
            $repository->flushCache();
        });
    }

    /**
     * Sync the custom field options for the given model
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\Innoclapps\Contracts\Fields\Customfieldable $field
     * @param array $attributes
     * @param string $action
     *
     * @return void
     */
    public function syncOptionsForModel($model, Customfieldable $field, array $attributes, $action)
    {
        $callbackAttributes = [$model, $field, $attributes, $action];

        collect($model::$beforeSyncCustomFieldOptions[$model::class] ?? [])->each->__invoke(
            ...$callbackAttributes
        );

        $model->{$field->customField->relationName}()->sync(
            collect($attributes)->mapWithKeys(function ($id) use ($field) {
                return [$id => ['custom_field_id' => $field->customField->id]];
            })
        );

        collect($model::$afterSyncCustomFieldOptions[$model::class] ?? [])->each->__invoke(
            ...$callbackAttributes
        );
    }

    /**
     * Get the given resource custom fields
     *
     * @param string $resourceName
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forResource($resourceName)
    {
        if (! isset(static::$cache)) {
            static::$cache = new CustomFieldResourcesCollection(
                $this->with(['options'])->get()->all()
            );
        }

        return static::$cache->forResource($resourceName);
    }

    /**
     * Handle the field options update
     *
     * @param \App\Innoclapps\Models\CustomField $field
     * @param array $options
     *
     * @return void
     */
    protected function handleFieldOptionsUpdate($field, $options)
    {
        $optionsBeforeUpdate = $field->options;

        $this->prepareOptionsForInsert($options)->each(function ($option, $index) use ($field, $optionsBeforeUpdate) {
            $attributes = [
                'name'          => $option['name'],
                'display_order' => $option['display_order'] ?? $index + 1,
                'swatch_color'  => $option['swatch_color'] ?? null,
            ];

            if (isset($option['id'])) {
                $optionsBeforeUpdate->find($option['id'])->fill($attributes)->save();
            } else {
                $field->options()->create($attributes);
            }
        });

        $optionsBeforeUpdate->filter(function ($option) use ($options) {
            return ! in_array($option->id, Arr::pluck($options, 'id'));
        })->each(function ($option) use ($field) {
            if ($field->isNotMultiOptionable()) {
                $resource = Innoclapps::resourceByName($field->resource_name);
                // Update constraint
                $resource->repository()->pushCriteria(WithTrashedCriteria::class)->massUpdate([$field->field_id => null]);
            }

            $option->delete();
        });
    }

    /**
     * Flush the fieds cache
     *
     * @return static
     */
    public function flushCache()
    {
        static::$cache = null;

        return $this;
    }

    /**
     * Create the custom field in database
     *
     * @param \Illuminate\Database\Eloquent\Model $relatedModel
     * @param string $fieldId
     * @param bool|null $isUnique
     *
     * @return void
     */
    protected function createColumn($relatedModel, $fieldId, $type, ?bool $isUnique)
    {
        $field = Fields::customFieldable()[$type];

        Schema::table($relatedModel->getTable(), function (Blueprint $table) use ($field, $fieldId, $isUnique) {
            $field['className']::createValueColumn($table, $fieldId);

            if ($isUnique === true && $field['uniqueable']) {
                $table->unique($fieldId);
            }
        });
    }

    /**
     * Delete the given field column
     *
     * @param \App\Innoclapps\Models\CustomField
     *
     * @return void
     */
    protected function deleteColumn($field)
    {
        $relatedModel = app(Innoclapps::resourceByName($field->resource_name)::$model);

        if (! Schema::hasColumn($relatedModel->getTable(), $field->field_id)) {
            return;
        }

        Schema::table($relatedModel->getTable(), function (Blueprint $table) use ($field, $relatedModel) {
            if ($field->isOptionable() && ! app()->runningUnitTests()) {
                $key = DB::select(
                    DB::raw(
                        'SHOW KEYS
                            FROM ' . DB::getTablePrefix() . $relatedModel->getTable() . '
                            WHERE Column_name=\'' . $field->field_id . '\''
                    )
                );

                $table->dropForeign($key[0]->Key_name);
            }

            $table->dropColumn($field->field_id);
        });
    }

    /**
     * Prepare the options for insert
     *
     * @param array $options
     *
     * @return \Illuminate\Support\Collection
     */
    protected function prepareOptionsForInsert($options)
    {
        return collect($options)->reject(fn ($option) => empty($option['name']))->unique('name');
    }

    /**
     * Handle the deleted custom field filter rules
     *
     * @param \App\Innoclapps\Models\CustomField $field
     *
     * @return void
     */
    protected function handleDeletedFieldFiltersRules($field)
    {
        // When model with custom fields is deleted, we will get the filters
        // which most likely are using custom field and remove them from the query object
        $repository = resolve(FilterRepository::class);

        $repository->findWhere([['rules', 'like', '%' . $field->field_id . '%']])
            ->each(function ($filter) use ($repository, $field) {
                $rules = Arr::toObject($filter->rules);

                if (Parser::validate($rules)) {
                    $rules->children = $this->handleDeletedFieldFilterRules($rules->children, $field);

                    $repository->update(['rules' => (array) $rules ?? []], $filter->id);
                }
            });
    }

    /**
     * Handle the deleted field rules
     *
     * @param array &$rules
     * @param \App\Innoclapps\Models\CustomField $field
     *
     * @return array
     */
    protected function handleDeletedFieldFilterRules(&$rules, $field)
    {
        foreach ($rules as $key => $rule) {
            if (Parser::isNested($rule)) {
                $rule->query->children = $this->handleDeletedFieldFilterRules($rule->query->children, $field);
            } else {
                if ($rule->query->rule == $field->field_id ||
                    (isset($rule->query->operand) && $rule->query->operand == $field->field_id)) {
                    unset($rules[$key]);
                }
            }

            // If the current rule in the loop is group, we will check if the
            // group is empty and if yes, we will actually remove the group from
            // the rules object as it won't be needed.
            if ($rule->type === 'group' && empty($rule->query->children)) {
                unset($rules[$key]);
            }
        }

        return array_values($rules);
    }
}
