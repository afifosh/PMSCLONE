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

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Innoclapps\Table\Table;
use Illuminate\Support\Collection;
use App\Innoclapps\Table\HasOneColumn;
use App\Innoclapps\Contracts\Countable;
use App\Innoclapps\Table\BelongsToColumn;
use App\Innoclapps\Table\RelationshipColumn;

class TableRequestCriteria extends RequestCriteria
{
    /**
     * Initialize new TableRequestCriteria instance.
     */
    public function __construct(Request $request, protected Collection $columns, protected Table $table)
    {
        parent::__construct($request);
    }

    /**
     * Apply order for the current request
     *
     * @param mixed $order
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     *
     * @return void
     */
    protected function applyOrder($order, $model)
    {
        // No order applied
        if (empty($order)) {
            return $model;
        }

        $order = collect($order)->reject(fn ($order) => empty($order['attribute']));

        // Remove any default order
        if ($order->isNotEmpty()) {
            $model->reorder();
        }

        $order->map(function ($order) {
            return array_merge($order, [
                'direction' => ($order['direction'] ?? '') ?: 'asc',
            ]);
        })->each(function ($order) use (&$model) {
            $column = $this->table->getColumn($order['attribute']);

            if ($column instanceof RelationshipColumn) {
                $this->orderByRelationship($column, $order, $model);
            } else {
                $model = $model->orderBy($column->attribute, $order['direction']);
            }
        });

        return $model;
    }

    /**
     * Order the query by relationship and check fields
     *
     * @param \App\Innoclapps\Table\Column $column
     * @param array $data
     * @param \Illumindata\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $query
     *
     * @return void
     */
    protected function orderByRelationship($column, $data, $query)
    {
        return match (true) {
            $column instanceof Countable       => $query->orderBy($column->attribute, $data['direction']),
            $column instanceof BelongsToColumn => $this->applyOrderWhenBelongsToColumn($column, $data, $query),
            $column instanceof HasOneColumn    => $this->applyOrderWhenHasOneColumn($column, $data, $query)
        };
    }

    /**
     * Apply order when the column is BelongsTo
     *
     * @param \App\Innoclapps\Table\BelongsToColumn $column
     * @param array $dir
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyOrderWhenBelongsToColumn($column, $data, $query)
    {
        $relation = $column->relationName;

        $keyName       = $query->getModel()->{$relation}()->getForeignKeyName();
        $relationTable = $query->getModel()->{$relation}()->getModel()->getTable();

        $alias = Str::snake(class_basename($query->getModel())) . '_' . $relation . '_' . $relationTable;

        return $query->leftJoin(
            $relationTable . ' as ' . $alias,
            function ($join) use ($query, $relation, $keyName, $alias) {
                $join->on($keyName, '=', $alias . '.id');
                $this->mergeExistingAttachedQueries($query, $join, $relation);
            }
        )->orderBy(
            $column->orderColumnCallback ?
                call_user_func_array($column->orderColumnCallback, [$data]) :
                $alias . '.' . $column->relationField,
            $data['direction']
        );
    }

    /**
     * Apply order when the column is HasOne
     *
     * @param \App\Innoclapps\Table\HasOneColumn $column
     * @param array $dir
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applyOrderWhenHasOneColumn($column, $data, $query)
    {
        $relation      = $column->relationName;
        $relationTable = $query->getModel()->{$relation}()->getModel()->getTable();

        return $query->leftJoin($relationTable, function ($join) use ($query, $relation) {
            $join->on(
                $query->getModel()->getQualifiedKeyName(),
                '=',
                $query->getModel()->{$relation}()->getQualifiedForeignKeyName()
            );

            $this->mergeExistingAttachedQueries($query, $join, $relation);
        })->orderBy($column->relationField, $data['direction']);
    }

    /**
     * Merge existing queries in the relation model
     *
     * @param \Illuminate\Database\Query\Builder $query The main query builder
     * @param \Illuminate\Database\Query\Builder $joinToQuery
     * @param string $relation
     *
     * @return void
     */
    protected function mergeExistingAttachedQueries($query, $joinToQuery, $relation)
    {
        $builder = $query->getModel()->{$relation}()
            // Illuminate\Database\Eloquent\Builder
            ->getQuery()
            // Illuminate\Database\Query\Builder
            ->getQuery();

        // Merge existing relation attached queries
        $joinToQuery->mergeWheres(array_filter($builder->wheres, function ($where) {
            return ! in_array($where['type'], ['Null', 'NotNull']);
        }), $builder->getBindings());
    }
}
