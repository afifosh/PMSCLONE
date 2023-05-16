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

namespace Modules\Core\Http\Controllers\Api\Workflow;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Http\Requests\WorkflowRequest;
use Modules\Core\Http\Resources\WorkflowResource;
use Modules\Core\Models\Workflow;

class WorkflowController extends ApiController
{
    /**
     * Get the stored workflows.
     */
    public function index(): JsonResponse
    {
        return $this->response(
            WorkflowResource::collection(Workflow::get())
        );
    }

    /**
     * Display the specified workflow..
     */
    public function show(Workflow $workflow): JsonResponse
    {
        return $this->response(new WorkflowResource($workflow));
    }

    /**
     * Store a newly created workflow in storage.
     */
    public function store(WorkflowRequest $request): JsonResponse
    {
        $workflow = new Workflow($request->createData());

        $workflow->save();

        return $this->response(new WorkflowResource($workflow), 201);
    }

    /**
     * Update the specified workflow in storage.
     */
    public function update(Workflow $workflow, WorkflowRequest $request): JsonResponse
    {
        $workflow->fill($request->createData());

        $workflow->save();

        return $this->response(new WorkflowResource($workflow));
    }

    /**
     * Remove the specified workflow from storage.
     */
    public function destroy(Workflow $workflow): JsonResponse
    {
        $workflow->delete();

        return $this->response('', 204);
    }
}
