<?php

namespace App\Services\Core;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    protected $model;

    protected array $attributes = [];

    public function setAttrs(array $attrs): self
    {
        $this->attributes = $attrs;

        return $this;
    }

    public function setModel(Model $model): BaseService
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function save($options = [])
    {
        $attributes = count($options) ? $options : request()->all();

        $this->model
            ->fill($this->getFillAble($attributes))
            ->save();

        return $this->model;
    }

    public function getFillAble($parameters = []): array
    {
        return count($this->attributes) ? $this->attributes : $parameters;
    }

    public function find($id)
    {
        return $this->model =  $this->model::query()->find($id);
    }

    public function __call($method, $arguments)
    {
        return $this->model->{$method}(...$arguments);
    }

}
