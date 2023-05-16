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

namespace Modules\Core\Http\Controllers\Api\Resource;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Resource\Http\ResourceRequest;

class SearchController extends ApiController
{
    /**
     * Perform search for a resource.
     */
    public function handle(ResourceRequest $request): JsonResponse
    {
        $resource = tap($request->resource(), function ($resource) {
            abort_if(! $resource::searchable(), 404);
        });

        if (empty($request->q)) {
            return $this->response([]);
        }

        $query = $resource->searchQuery()
            ->criteria($resource->getRequestCriteria($request));

        if ($criteria = $resource->viewAuthorizedRecordsCriteria()) {
            $query->criteria($criteria);
        }

        return $this->response(
            $request->toResponse(
                $resource->order($query)->get()
            )
        );
    }
}
