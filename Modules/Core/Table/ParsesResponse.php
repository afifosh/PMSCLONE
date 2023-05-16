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

namespace Modules\Core\Table;

use Closure;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Core\Contracts\Presentable;
use Modules\Core\Table\LengthAwarePaginator as TableLengthAwarePaginator;

trait ParsesResponse
{
    /**
     * Parse the response for the request
     */
    private function parseResponse(LengthAwarePaginator $result, int $allTimeTotal): TableLengthAwarePaginator
    {
        $columns = $this->getUserColumns()->reject(fn ($column) => $column->isHidden())->all();

        $result->getCollection()->transform(function ($model) use ($columns) {
            $displayAs = [];

            // Global main table models appends
            $model->append(
                array_merge($this->appends(), $model instanceof Presentable ? ['path'] : [])
            );

            foreach ($columns as $column) {
                // Check for custom appends for relation the models
                if ($column->isRelation()) {
                    $this->appendAttributesWhenRelation($column, $model);
                }

                // Inline displayAs closure provided
                if ($column->displayAs instanceof Closure) {
                    data_set($displayAs, $column->attribute, call_user_func_array($column->displayAs, [$model]));
                }

                // Vuejs casts 0 as empty and 0 will be shown as empty
                elseif ($column->isCountable() && $column->counts()) {
                    data_set($displayAs, $column->attribute, (string) $model->{$column->attribute});
                }
            }

            // Set the model displayAs attribute so it can be serialized for the front-end
            $model->setAttribute('displayAs', $displayAs);

            // If any authorizations, set them as attribute so they can be serialized for the front-end
            if ($authorizations = $this->getAuthorizations($model)) {
                $model->setAttribute('authorizations', $authorizations);
            }

            return $model;
        });

        $response = (new TableLengthAwarePaginator(
            $result->getCollection(),
            $result->total(),
            $result->perPage(),
            $result->currentPage()
        ))->setAllTimeTotal($allTimeTotal);

        $this->tapResponse($response);

        return $response;
    }

    /**
     * Tap the response
     */
    protected function tapResponse(TableLengthAwarePaginator $response): void
    {
    }

    /**
     * Append attributes when column is relation
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function appendAttributesWhenRelation(RelationshipColumn $column, $model): void
    {
        if ($column instanceof BelongsToColumn && $model->{$column->relationName}) {
            $this->appendAttributesWhenBelongsTo($column, $model);
        } elseif (($column instanceof HasManyColumn || $column instanceof BelongsToManyColumn) && ! $column->counts()) {
            $this->appendAttributesWhenHasManyOrBelongsToMany($column, $model);
        }
    }

    /**
     * Append attributes when BelongsToColumn
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function appendAttributesWhenBelongsTo(BelongsToColumn $column, $model): void
    {
        $model->{$column->relationName}->append(array_merge(
            $column->appends,
            $model->{$column->relationName}()->getModel() instanceof Presentable ? ['path', 'display_name'] : []
        ));
    }

    /**
     * Append attributes when HasManyColumn
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     */
    protected function appendAttributesWhenHasManyOrBelongsToMany(HasManyColumn|BelongsToManyColumn $column, $model): void
    {
        $model->{$column->relationName}->map(function ($relationModel) use ($column) {
            unset($relationModel->pivot);

            return $relationModel->append(array_merge(
                $column->appends,
                $relationModel instanceof Presentable ? ['path', 'display_name'] : [],
            ));
        });
    }
}
