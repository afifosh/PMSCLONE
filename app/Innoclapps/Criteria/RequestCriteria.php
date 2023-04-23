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

namespace App\Innoclapps\Criteria;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Innoclapps\Contracts\Repository\CriteriaInterface;
use App\Innoclapps\Contracts\Repository\RepositoryInterface;

class RequestCriteria implements CriteriaInterface
{
    /**
     * Append additional criterias within the request criteria
     *
     * @var array
     */
    protected array $appends = [];

    /**
     * Initialize new RequestCriteria class
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Apply criteria in query repository
     *
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param \App\Innoclapps\Contracts\Repository\RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $fieldsSearchable = $repository->getFieldsSearchable();

        $searchQuery  = $this->request->get('q', null);
        $searchFields = $this->request->get('search_fields', null);
        $searchMatch  = $this->request->get('search_match', null);

        $select = $this->request->get('select', null);
        $order  = $this->request->get('order', []);
        $with   = $this->request->get('with', null);
        $take   = $this->request->get('take', null);

        if ($searchQuery && is_array($fieldsSearchable) && count($fieldsSearchable)) {
            $isFirstField = true;
            $searchFields = (is_array($searchFields) || is_null($searchFields)) ?
                                $searchFields :
                                explode(';', $searchFields);

            $fields      = $this->parseSearchFields($fieldsSearchable, $searchFields);
            $searchData  = $this->parserSearchData($searchQuery);
            $searchQuery = $this->parseSearchValue($searchQuery);

            $modelForceAndWhere = strtolower($searchMatch) === 'and';

            if ($this->shouldSearchOnlyById($searchQuery)) {
                $fields = [$model->getModel()->getKeyName()];
            }

            $model = $model->where(function ($query) use (
                $fields,
                $searchQuery,
                $searchData,
                $isFirstField,
                $modelForceAndWhere,
                $repository
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
                        $value = ($condition == 'like' || $condition == 'ilike') ?
                        "%{$searchData[$field]}%" :
                        $searchData[$field];
                    } else {
                        if (! is_null($searchQuery)) {
                            $value = ($condition == 'like' || $condition == 'ilike') ?
                            "%{$searchQuery}%" :
                            $searchQuery;
                        }
                    }

                    $relation = null;

                    if (stripos($field, '.')) {
                        $explode = explode('.', $field);
                        $field = array_pop($explode);
                        $relation = implode('.', $explode);
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

                foreach ($this->appends as $criteria) {
                    $query = $criteria->apply($query, $repository);
                }
            });
        }

        if ($take) {
            $model = $model->take($take);
        }

        $model = $this->applyOrder($order, $model);
        $model = $this->applySelect($select, $model);
        $model = $this->applyWith($with, $model);

        return $model;
    }

    /**
     * Apply and where search
     *
     * @param string $value
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string|null $relation
     * @param string $condition
     *
     * @return void
     */
    protected function applySearchAndWhere($value, $query, $field, $relation, $condition)
    {
        if (! is_null($relation)) {
            $query->whereHas($relation, function ($query) use ($field, $condition, $value) {
                $query->where($field, $condition, $value);
            });

            return;
        }

        $query->where($query->qualifyColumn($field), $condition, $value);
    }

    /**
     * Apply or where search
     *
     * @param string $value
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $field
     * @param string|null $relation
     * @param string $condition
     *
     * @return void
     */
    protected function applySearchOrWhere($value, $query, $field, $relation, $condition)
    {
        if (! is_null($relation)) {
            $query->orWhereHas($relation, function ($query) use ($field, $condition, $value) {
                $query->where($field, $condition, $value);
            });

            return;
        }

        $query->orWhere($query->qualifyColumn($field), $condition, $value);
    }

    /**
     * Append additional criteria within the request query
     *
     * @param \App\Innoclapps\Contracts\Repository\CriteriaInterface $criteria
     *
     * @return static
     */
    public function appends(CriteriaInterface $criteria)
    {
        $this->appends[] = $criteria;

        return $this;
    }

    /**
     * Apply order for the current request
     *
     * @param mixed $order
     * @param \Illuminate\Database\Eloquent\Builder $model
     *
     * @return void
     */
    protected function applyOrder($order, $model)
    {
        // No order applied
        if (empty($order)) {
            return $model;
        }

        // Allowing passing sort option like order=created_at|desc
        if (! is_array($order)) {
            $orderArray = explode('|', $order);

            $order = [
                'field'     => $orderArray[0],
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
            $model = $model->reorder();
        }

        $order->map(
            fn ($order) => array_merge($order, [
                'direction' => ($order['direction'] ?? '') ?: 'asc',
            ])
        )->each(function ($order) use (&$model) {
            list('field' => $field, 'direction' => $direction) = $order;
            $split = explode('|', $field);

            if (count($split) > 1) {
                $this->orderByRelationship($split, $direction, $model);
            } else {
                $qualifiedColumnName = $this->isAggregateField($field) ? $field : $model->qualifyColumn($field);

                $model = $model->orderBy($qualifiedColumnName, $direction);
            }
        });

        return $model;
    }

    /**
     * Check if the field is aggragate
     *
     * @see https://laravel.com/docs/9.x/eloquent-relationships#other-aggregate-functions
     *
     * @param string $field
     *
     * @return boolean
     */
    protected function isAggregateField($field)
    {
        return str_ends_with($field, '_count') || Str::contains($field, ['_sum_', '_min_', '_max_', '_avg_', '_exists_']);
    }

    /**
     * Order the query by relationship
     *
     * @param array $orderData
     * @param string $dir
     * @param \Illuminate\Database\Eloquent\Builder $model
     *
     * @return void
     */
    protected function orderByRelationship($orderData, $dir, $model)
    {
        /*
        * ex.
        * products|description -> join products on current_table.product_id = products.id order by description
        *
        * products:custom_id|products.description -> join products on current_table.custom_id = products.id order
        * by products.description (in case both tables have same column name)
        */
        $table      = $model->getModel()->getTable();
        $sortTable  = $orderData[0];
        $sortColumn = $orderData[1];

        $orderData = explode(':', $sortTable);

        if (count($orderData) > 1) {
            $sortTable = $orderData[0];
            $keyName   = $table . '.' . $orderData[1];
        } else {
            /*
             * If you do not define which column to use as a joining column on current table, it will
             * use a singular of a join table appended with _id
             *
             * ex.
             * products -> product_id
             */
            $prefix  = Str::singular($sortTable);
            $keyName = $table . '.' . $prefix . '_id';
        }

        $model = $model
            ->leftJoin($sortTable, $keyName, '=', $sortTable . '.id')
            ->orderBy($sortTable . '.' . $sortColumn, $dir)
            ->addSelect($table . '.*');
    }

    /**
     * Apply select fields to model
     *
     * @param mixed $select
     * @param \Illuminate\Database\Eloquent\Builder $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applySelect($select, $model)
    {
        if (! empty($select)) {
            if (is_string($select)) {
                $select = explode(';', $select);
            }

            $model = $model->select($select);
        }

        return $model;
    }

    /**
     * Apply with relationships to model
     *
     * @param mixed $with
     * @param \Illuminate\Database\Eloquent\Builder $model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyWith($with, $model)
    {
        if ($with) {
            if (is_string($with)) {
                $with = explode(';', $with);
            }

            $model = $model->with($with);
        }

        return $model;
    }

    /**
     * @param string $query
     *
     * @return array
     */
    protected function parserSearchData($query)
    {
        $searchData = [];

        if (stripos($query, ':')) {
            $fields = explode(';', $query);

            foreach ($fields as $row) {
                try {
                    list($field, $value) = explode(':', $row);
                    $searchData[$field]  = $value;
                } catch (Exception $e) {
                    //Surround offset error
                }
            }
        }

        return $searchData;
    }

    /**
     * @param string $query
     *
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
     * @param array $fields
     * @param array|null $searchFields
     *
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
        ];

        $whitelisted    = [];
        $originalFields = $allowed;

        foreach ($searchFields as $index => $field) {
            $parts          = explode(':', $field);
            $temporaryIndex = array_search($parts[0], $originalFields);

            if (count($parts) == 2 && in_array($parts[1], $acceptedConditions)) {
                unset($originalFields[$temporaryIndex]);
                $field                  = $parts[0];
                $condition              = $parts[1];
                $originalFields[$field] = $condition;
                $searchFields[$index]   = $field;
            }
        }

        foreach ($originalFields as $field => $condition) {
            if (is_numeric($field)) {
                $field     = $condition;
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
     * @param mixed $value
     *
     * @return boolean
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

        return $this->request->isZapier();
    }
}
