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

namespace Modules\Core\Resource;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use JsonSerializable;
use Modules\Core\Contracts\Presentable;
use Modules\Core\Models\Model;
use Modules\Core\Resource\Http\ResourceRequest;

class GlobalSearch implements JsonSerializable
{
    /**
     * Initialize global search for the given resources.
     */
    public function __construct(protected ResourceRequest $request, protected Collection $resources)
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
                    'data' => $this->query($resource)
                        ->take($resource->globalSearchResultsLimit)
                        ->get()
                        ->whereInstanceOf(Presentable::class)
                        ->map(function ($model) use ($resource) {
                            return $this->data($model, $resource);
                        }),
                ]);
            });

        return $result->reject(fn ($result) => $result['data']->isEmpty())->values();
    }

    /**
     * Prepare the search query.
     */
    protected function query(Resource $resource): Builder
    {
        $query = $resource->globalSearchQuery(
            $resource->newQuery()->criteria($resource->getRequestCriteria($this->request))
        );

        if ($criteria = $resource->viewAuthorizedRecordsCriteria()) {
            $query->criteria($criteria);
        }

        return $query;
    }

    /**
     * Provide the model data for the response.
     */
    protected function data(Model&Presentable $model, Resource $resource): array
    {
        return [
            'path' => $model->path,
            'display_name' => $model->display_name,
            'created_at' => $model->created_at,
            $model->getKeyName() => $model->getKey(),
            'resourceName' => $resource->name(),
        ];
    }

    /**
     * Serialize GlobalSearch class.
     */
    public function jsonSerialize(): array
    {
        return $this->get()->all();
    }
}
