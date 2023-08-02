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

namespace Modules\Core\Criteria;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Modules\Core\Contracts\Criteria\QueryCriteria;

class RequestCriteria implements QueryCriteria
{
    /**
     * Append additional criterias within the request criteria.
     */
    protected array $appends = [];

    /**
     * Initialize new RequestCriteria class
     */
    public function __construct(protected ?HttpRequest $request = null)
    {
        $this->request = $request ?: Request::instance();
    }

    /**
     * Apply the criteria for the given query.
     */
    public function apply(Builder $query): Builder
    {
        $fieldsSearchable = $query->getModel()->getSearchableFields();

        $searchQuery = $this->request->get('q', null);
        $searchFields = $this->request->get('search_fields', null);
        $searchMatch = $this->request->get('search_match', null);

        $select = $this->request->get('select', null);
        $order = $this->request->get('order', []);
        $with = $this->request->get('with', null);
        $take = $this->request->get('take', null);

        if ($searchQuery && is_array($fieldsSearchable) && count($fieldsSearchable)) {
            $isFirstField = true;

            $searchFields = (is_array($searchFields) || is_null($searchFields)) ?
                                $searchFields :
                                explode(';', $searchFields);

            $searchData = $this->parseSearchData($searchQuery);

            $searchQuery = $this->parseSearchValue($searchQuery);

            $fields = $this->parseSearchFields($fieldsSearchable, $searchFields);

            if ($this->shouldSearchOnlyById($searchQuery)) {
                $fields = [$query->getModel()->getKeyName()];
            }

            $modelForceAndWhere = strtolower($searchMatch) === 'and';

            $query = $query->where(function ($query) use (
                $fields,
                $searchQuery,
                $searchData,
                $isFirstField,
                $modelForceAndWhere,
            ) {
                /** @var Builder $query */
                foreach ($fields as $field => $condition) {
                    if (is_numeric($field)) {
                        $field = $condition;
                        $condition = '=';
                    }

                    $value = null;

                    $condition = trim(strtolower($condition));

                    if (isset($searchData[$field])) {
                        $value = $condition == 'like' ? "%{$searchData[$field]}%" : $searchData[$field];
                    } else {
                        if (! is_null($searchQuery)) {
                            $value = $condition == 'like' ? "%{$searchQuery}%" : $searchQuery;
                        }
                    }

                    $relation = null;

                    if (stripos($field, '.')) {
                        $explode = explode('.', $field);
                        $field = array_pop($explode);
                        $relation = implode('.', $explode);
                    }

                    if ($condition === 'in') {
                        $value = explode(',', $value);

                        if (trim($value[0]) === '' || $field == $value[0]) {
                            $value = null;
                        }
                    }

                    if (! is_null($value)) {
                        if ($isFirstField || $modelForceAndWhere) {
                            $this->applySearchAndWhere($value, $query, $field, $relation, $condition);
                            $isFirstField = false;
                        } else {
                            $this->applySearchOrWhere($value, $query, $field, $relation, $condition);
                        }
                    }
                }

                $this->applyAppendedCriterias($query);
            });
        }

        if ($take) {
            $query = $query->take($take);
        }

        $query = $this->applyOrder($order, $query);
        $query = $this->applySelect($select, $query);
        $query = $this->applyWith($with, $query);

        return $query;
    }

    /**
     * Apply and where search
     *
     * @param  string  $value
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  string|null  $relation
     * @param  string  $condition
     * @return void
     */
    protected function applySearchAndWhere($value, $query, $field, $relation, $condition)
    {
        if (! is_null($relation)) {
            $query->whereHas($relation, function ($query) use ($field, $condition, $value) {
                if ($condition === 'in') {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $condition, $value);
                }
            });

            return;
        }

        if ($condition === 'in') {
            $query->whereIn($query->qualifyColumn($field), $value);
        } else {
           $query->where($query->qualifyColumn($field), $condition, $value);
       }
    }

    /**
     * Apply or where search
     *
     * @param  string  $value
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  string|null  $relation
     * @param  string  $condition
     * @return void
     */
    protected function applySearchOrWhere($value, $query, $field, $relation, $condition)
    {
        if (! is_null($relation)) {
            $query->orWhereHas($relation, function ($query) use ($field, $condition, $value) {
                 if ($condition === 'in') {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $condition, $value);
                }
            });

            return;
        }

        if ($condition === 'in') {
            $query->orWhereIn($query->qualifyColumn($field), $value);
        } else {
           $query->orWhere($query->qualifyColumn($field), $condition, $value);
       }
    }

    /**
     * Append additional criteria within the request query.
     */
    public function appends(QueryCriteria|callable $criteria): static
    {
        $this->appends[] = $criteria;

        return $this;
    }

    /**
     * Apply the appended criterias.
     */
    protected function applyAppendedCriterias(Builder $query): void
    {
        foreach ($this->appends as $criteria) {
            if (is_callable($criteria)) {
                call_user_func($criteria, $query);
            } else {
                $criteria->apply($query);
            }
        }
    }

    /**
     * Apply order for the current request
     *
     * @param  mixed  $order
     */
    protected function applyOrder($order, Builder $query): Builder
    {
        // No order applied
        if (empty($order)) {
            return $query;
        }

        // Allowing passing sort option like order=created_at|desc
        if (! is_array($order)) {
            $orderArray = explode('|', $order);

            $order = [
                'field' => $orderArray[0],
                'direction' => $orderArray[1] ?? '',
            ];
        }

        // Is not multidimensional array, order by one field and direction
        // e.q. ['field'=>'fieldName', 'direction'=>'asc']
        if (isset($order['field'])) {
            $order = [$order];
        }

        $order = collect($order)->reject(function ($order) {
            return empty($order['field']);
        });

        // Remove any default order
        if ($order->isNotEmpty()) {
            $query = $query->reorder();
        }

        $order->map(fn ($order) => array_merge($order, [
            'direction' => ($order['direction'] ?? '') ?: 'asc',
        ]))
            ->each(function ($order) use (&$query) {
                ['field' => $field, 'direction' => $direction] = $order;
                $split = explode('|', $field);

                if (count($split) > 1) {
                    $this->orderByRelationship($split, $direction, $query);
                } else {
                    $qualifiedColumnName = $this->isAggregateField($field) ? $field : $query->qualifyColumn($field);

                    $query = $query->orderBy($qualifiedColumnName, $direction);
                }
            });

        return $query;
    }

    /**
     * Check if the field is aggregate.
     *
     * @see https://laravel.com/docs/10.x/eloquent-relationships#other-aggregate-functions
     */
    protected function isAggregateField(string $field): bool
    {
        return str_ends_with($field, '_count') ||
            Str::contains($field, ['_sum_', '_min_', '_max_', '_avg_', '_exists_']);
    }

    /**
     * Order the query by relationship
     *
     * @param  array  $orderData
     * @param  string  $dir
     * @return void
     */
    protected function orderByRelationship($orderData, $dir, Builder $model): Builder
    {
        /*
        * ex.
        * products|description -> join products on current_table.product_id = products.id order by description
        *
        * products:custom_id|products.description -> join products on current_table.custom_id = products.id order
        * by products.description (in case both tables have same column name)
        */
        $table = $model->getModel()->getTable();
        $sortTable = $orderData[0];
        $sortColumn = $orderData[1];

        $orderData = explode(':', $sortTable);

        if (count($orderData) > 1) {
            $sortTable = $orderData[0];
            $keyName = $table.'.'.$orderData[1];
        } else {
            /*
             * If you do not define which column to use as a joining column on current table, it will
             * use a singular of a join table appended with _id
             *
             * ex.
             * products -> product_id
             */
            $prefix = Str::singular($sortTable);
            $keyName = $table.'.'.$prefix.'_id';
        }

        return $model->leftJoin($sortTable, $keyName, '=', $sortTable.'.id')
            ->orderBy($sortTable.'.'.$sortColumn, $dir)
            ->addSelect($table.'.*');
    }

    /**
     * Apply select fields to model
     *
     * @param  mixed  $select
     */
    protected function applySelect($select, Builder $query): Builder
    {
        if (! empty($select)) {
            if (is_string($select)) {
                $select = explode(';', $select);
            }

            $query = $query->select($select);
        }

        return $query;
    }

    /**
     * Apply with relationships to model
     *
     * @param  mixed  $with
     */
    protected function applyWith($with, Builder $query): Builder
    {
        if ($with) {
            if (is_string($with)) {
                $with = explode(';', $with);
            }

            $query = $query->with($with);
        }

        return $query;
    }

    /**
     * @param  string  $query
     * @return array
     */
    protected function parseSearchData($query)
    {
        $searchData = [];

        if (stripos($query, ':')) {
            $fields = explode(';', $query);

            foreach ($fields as $row) {
                try {
                    [$field, $value] = explode(':', $row);
                    $searchData[$field] = $value;
                } catch (Exception) {
                    //Surround offset error
                }
            }
        }

        return $searchData;
    }

    /**
     * @param  string  $query
     * @return null
     */
    protected function parseSearchValue($query)
    {
        if (stripos($query, ';') || stripos($query, ':')) {
            $values = explode(';', $query);

            foreach ($values as $value) {
                $s = explode(':', $value);

                if (count($s) == 1) {
                    return $s[0];
                }
            }

            return;
        }

        return $query;
    }

    /**
     * Parse the searchable fields
     *
     * @param  array  $fields
     * @param  array|null  $searchFields
     * @return array
     */
    protected function parseSearchFields($allowed = [], $searchFields = null)
    {
        if (is_null($searchFields) || count($searchFields) === 0) {
            return $allowed;
        }

        $acceptedConditions = [
            '=',
            'like',
            'in',
        ];

        $whitelisted = [];
        $originalFields = $allowed;

        foreach ($searchFields as $index => $field) {
            $parts = explode(':', $field);
            $temporaryIndex = array_search($parts[0], $originalFields);

            if (count($parts) == 2 && in_array($parts[1], $acceptedConditions)) {
                unset($originalFields[$temporaryIndex]);
                $field = $parts[0];
                $condition = $parts[1];
                $originalFields[$field] = $condition;
                $searchFields[$index] = $field;
            }
        }

        foreach ($originalFields as $field => $condition) {
            if (is_numeric($field)) {
                $field = $condition;
                $condition = '=';
            }

            if (in_array($field, $searchFields)) {
                $whitelisted[$field] = $condition;
            }
        }

        abort_unless(
            count($whitelisted),
            403,
            sprintf(
                'None of the search fields were accepted. Acceptable search fields are: %s',
                implode(',', array_keys($allowed))
            )
        );

        return $whitelisted;
    }

    /**
     * Check whether the search should be performed only by id
     * This works even when the ID is not allowed as searchable field.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function shouldSearchOnlyById($value)
    {
        if (is_array($value)) {
            return false;
        }

        // Is not integer and not all string characters are digits
        if (! is_int($value) && ! ctype_digit($value)) {
            return false;
        }

        // String and starts with 0, probably not ID
        // and search for example phone numberc etc...
        if (is_string($value) && substr($value, 0, 1) === '0') {
            return false;
        }

        // If value less then 1, probably not ID value
        // As well if the value length is bigger then 20, as BigIncrement column length is 20
        if ((int) $value < 1 || strlen((string) $value) > 20) {
            return false;
        }

        return false;
    }
}
