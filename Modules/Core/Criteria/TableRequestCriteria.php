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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Modules\Core\Contracts\Countable;
use Modules\Core\Table\BelongsToColumn;
use Modules\Core\Table\HasOneColumn;
use Modules\Core\Table\RelationshipColumn;
use Modules\Core\Table\Table;

class TableRequestCriteria extends RequestCriteria
{
    /**
     * Initialize new TableRequestCriteria instance.
     */
    public function __construct(protected Collection $columns, protected Table $table)
    {
        parent::__construct();
    }

    /**
     * Apply order for the current request
     *
     * @param  mixed  $order
     * @return void
     */
    protected function applyOrder($order, Builder $query): Builder
    {
        // No order applied
        if (empty($order)) {
            return $query;
        }

        // Filter any invalid ordering
        $order = collect($order)->reject(
            fn ($order) => empty($order['attribute'])
        );

        // Remove any default order
        if ($order->isNotEmpty()) {
            $query->reorder();
        }

        $order->map(fn ($order) => array_merge($order, [
            'direction' => ($order['direction'] ?? '') ?: 'asc',
        ]))->each(function ($order) use (&$query) {
            $column = $this->table->getColumn($order['attribute']);

            if ($column instanceof RelationshipColumn) {
                $this->orderByRelationship($column, $order, $query);
            } else {
                $query = $query->orderBy($column->attribute, $order['direction']);
            }
        });

        return $query;
    }

    /**
     * Order the query by relationship and check fields
     *
     * @param  \Modules\Core\Table\Column  $column
     * @param  array  $data
     */
    protected function orderByRelationship($column, $data, Builder $query): Builder
    {
        return match (true) {
            $column instanceof Countable => $query->orderBy($column->attribute, $data['direction']),
            $column instanceof BelongsToColumn => $this->applyOrderWhenBelongsToColumn($column, $data, $query),
            $column instanceof HasOneColumn => $this->applyOrderWhenHasOneColumn($column, $data, $query)
        };
    }

    /**
     * Apply order when the column is BelongsTo.
     */
    protected function applyOrderWhenBelongsToColumn(BelongsToColumn $column, array $dir, Builder $query): Builder
    {
        $relation = $column->relationName;

        $keyName = $query->getModel()->{$relation}()->getForeignKeyName();
        $relationTable = $query->getModel()->{$relation}()->getModel()->getTable();

        $alias = Str::snake(class_basename($query->getModel())).'_'.$relation.'_'.$relationTable;

        return $query->leftJoin(
            $relationTable.' as '.$alias,
            function ($join) use ($query, $relation, $keyName, $alias) {
                $join->on($keyName, '=', $alias.'.id');
                $this->mergeExistingAttachedQueries($query, $join, $relation);
            }
        )->orderBy(
            $column->orderColumnCallback ?
                call_user_func_array($column->orderColumnCallback, [$dir]) :
                $alias.'.'.$column->relationField,
            $dir['direction']
        );
    }

    /**
     * Apply order when the column is HasOne.
     */
    protected function applyOrderWhenHasOneColumn(HasOneColumn $column, array $dir, Builder $query): Builder
    {
        $relation = $column->relationName;
        $relationTable = $query->getModel()->{$relation}()->getModel()->getTable();

        return $query->leftJoin($relationTable, function ($join) use ($query, $relation) {
            $join->on(
                $query->getModel()->getQualifiedKeyName(),
                '=',
                $query->getModel()->{$relation}()->getQualifiedForeignKeyName()
            );

            $this->mergeExistingAttachedQueries($query, $join, $relation);
        })->orderBy($column->relationField, $dir['direction']);
    }

    /**
     * Merge existing queries in the relation model.
     */
    protected function mergeExistingAttachedQueries(Builder $query, JoinClause $joinToQuery, string $relation): void
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
