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

use JsonSerializable;
use Illuminate\Support\Collection;
use App\Innoclapps\Contracts\Presentable;
use App\Innoclapps\Criteria\RequestCriteria;

class GlobalSearch implements JsonSerializable
{
    /**
     * Total results
     *
     * @var integer
     */
    protected int $take = 5;

    /**
     * Initialize global search for the given resources
     *
     * @param \Illuminate\Support\Collection $resources
     */
    public function __construct(protected Collection $resources)
    {
    }

    /**
     * Get the search result
     *
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        $result = new Collection([]);

        $this->resources->reject(fn ($resource) => ! $resource::searchable())
            ->each(function ($resource) use (&$result) {
                $result->push([
                    'title' => $resource->label(),
                    'data'  => $this->query($resource->repository(), $resource)
                        ->all()
                        ->whereInstanceOf(Presentable::class)
                        ->map(function ($model) use ($resource) {
                            return $this->data($model, $resource);
                        }),
                ]);
            });

        return $result->reject(fn ($result) => $result['data']->isEmpty())->values();
    }

    /**
     * Prepare the search query
     *
     * @param \App\Innoclapps\Repository\BaseRepository $repository
     * @param \App\Innoclapps\Resources\Resource $resource
     *
     * @return \App\Innoclapps\Repository\BaseRepository
     */
    protected function query($repository, $resource)
    {
        return $resource->globalSearchQuery($this->take, $repository->pushCriteria(RequestCriteria::class));
    }

    /**
     * Provide the model data for the response
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \App\Innoclapps\Resources\Resource $resource
     *
     * @return array
     */
    protected function data($model, $resource) : array
    {
        return [
            'path'               => $model->path,
            'display_name'       => $model->display_name,
            'created_at'         => $model->created_at,
            $model->getKeyName() => $model->getKey(),
        ];
    }

    /**
     * Serialize GlobalSearch class
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->get()->all();
    }
}
