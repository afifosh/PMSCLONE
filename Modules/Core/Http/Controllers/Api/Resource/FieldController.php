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
use Modules\Core\Facades\Fields;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Resource\Http\ResourceRequest;

class FieldController extends ApiController
{
    /**
     * Get the resource create fields.
     */
    public function create(ResourceRequest $request): JsonResponse
    {
        return $this->response(
            Fields::resolveCreateFieldsForDisplay($request->resourceName())
        );
    }

    /**
     * Get the resource update fields.
     */
    public function update(ResourceRequest $request): JsonResponse
    {
        $request->resource()->setModel($request->record());

        return $this->response(
            Fields::resolveUpdateFieldsForDisplay($request->resourceName())
        );
    }

    /**
     * Get the resource detail fields.
     */
    public function detail(ResourceRequest $request): JsonResponse
    {
        $request->resource()->setModel($request->record());

        return $this->response(
            Fields::resolveDetailFieldsForDisplay($request->resourceName())
        );
    }
}
