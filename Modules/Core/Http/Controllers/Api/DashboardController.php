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

namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Card\DashboardService;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Http\Resources\DashboardResource;
use Modules\Core\Models\Dashboard;

class DashboardController extends ApiController
{
    /**
     * Display a listing of the current user dashboards.
     */
    public function index(Request $request): JsonResponse
    {
        $dashboards = Dashboard::byUser($request->user()->getKey())->orderBy('name')->get();

        return $this->response(
            DashboardResource::collection($dashboards)
        );
    }

    /**
     * Display the specified dashboard.
     */
    public function show(Dashboard $dashboard): JsonResponse
    {
        $this->authorize('view', $dashboard);

        return $this->response(new DashboardResource($dashboard));
    }

    /**
     * Store a newly created dashboard in storage.
     */
    public function store(Request $request, DashboardService $service): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:191',
            'cards.*.key' => 'sometimes|required',
            'cards.*.order' => 'sometimes|numeric',
            'is_default' => 'sometimes|boolean',
        ]);

        $dashboard = $service->create($data, $request->user()->getKey());

        return $this->response(new DashboardResource($dashboard), 201);
    }

    /**
     * Update the specified dashboard in storage.
     */
    public function update(Dashboard $dashboard, Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:191',
            'cards.*.key' => 'sometimes|required',
            'cards.*.order' => 'sometimes|numeric',
            'is_default' => 'sometimes|boolean',
        ]);

        $this->authorize('update', $dashboard);

        $dashboard->fill($data);

        if ($dashboard->is_default === false && $dashboard->user->hasOnlyOneDashboard()) {
            $dashboard->is_default = true;
        }

        $dashboard->save();

        return $this->response(new DashboardResource($dashboard));
    }

    /**
     * Remove the specified dashboard from storage.
     */
    public function destroy(Dashboard $dashboard): JsonResponse
    {
        $this->authorize('delete', $dashboard);

        if ($dashboard->user->hasOnlyOneDashboard()) {
            abort(409, 'There must be at least one active dashboard.');
        }

        $dashboard->delete();

        return $this->response('', 204);
    }
}
