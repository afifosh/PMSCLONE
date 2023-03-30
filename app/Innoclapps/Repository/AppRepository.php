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

namespace App\Innoclapps\Repository;

abstract class AppRepository extends BaseRepository
{
    /**
     * Get the model array able relations required when passing data with the response
     *
     * @return array
     */
    public function getResponseRelations()
    {
        if (method_exists($this, 'eagerLoad')) {
            return $this->eagerLoad();
        }

        return [];
    }

    /**
     * Update the record using the provided model
     *
     * @param array $attributes
     * @param \App\Innoclapps\Models\Model $model
     *
     * @return \App\Innoclapps\Models\Model
     */
    public function updateUsingModel(array $attributes, $model)
    {
        $model->fill($attributes);

        $this->performUpdate($model, $attributes);

        $result = $this->parseResult($model);

        return $result;
    }

    /**
     * Set with property for the query to include the relations required for the response
     *
     * @return static
     */
    public function withResponseRelations()
    {
        return $this->with($this->getResponseRelations());
    }

    /**
     * Set query to order the nulls as last for the given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return static
     */
    public function orderByNullsLast($column, $direction = 'asc')
    {
        $this->model = $this->model->orderByNullsLast($column, $direction);

        return $this;
    }

    /**
     * Chunk the results of the query.
     *
     * @param int $count
     * @param callable $callback
     *
     * @return boolean
     */
    public function chunk($count, callable $callback)
    {
        $this->applyCriteria();
        $this->applyScope();

        $result = $this->model->chunk($count, $callback);

        $this->resetModel();
        $this->resetScope();

        return $result;
    }

    /**
     * Get a lazy collection for the given query.
     *
     * @return \Illuminate\Support\LazyCollection
     */
    public function cursor()
    {
        $this->applyCriteria();
        $this->applyScope();

        $result = $this->model->cursor();

        $this->resetModel();
        $this->resetScope();

        return $this->parseResult($result);
    }

    /**
     * Query lazily, by chunks of the given size.
     *
     * @param int $chunkSize
     * @return \Illuminate\Support\LazyCollection
     *
     * @throws \InvalidArgumentException
     */
    public function lazy($chunkSize = 1000)
    {
        $this->applyCriteria();
        $this->applyScope();

        $result = $this->model->lazy($chunkSize);

        $this->resetModel();
        $this->resetScope();

        return $this->parseResult($result);
    }
}
