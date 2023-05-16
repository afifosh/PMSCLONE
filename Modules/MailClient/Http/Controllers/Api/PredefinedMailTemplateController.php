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

namespace Modules\MailClient\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\ApiController;
use Modules\MailClient\Http\Requests\PredefinedMailTemplateRequest;
use Modules\MailClient\Http\Resources\PredefinedMailTemplateResource;
use Modules\MailClient\Models\PredefinedMailTemplate;

class PredefinedMailTemplateController extends ApiController
{
    /**
     * Display a listing of the mail templates.
     */
    public function index(): JsonResponse
    {
        $templates = PredefinedMailTemplateResource::collection(
            PredefinedMailTemplate::with('user')->visibleToUser()->get()
        );

        return $this->response($templates);
    }

    /**
     * Display the specified mail template.
     */
    public function show(string $id): JsonResponse
    {
        $template = PredefinedMailTemplate::with('user')->findOrFail($id);

        $this->authorize('view', $template);

        return $this->response(new PredefinedMailTemplateResource($template));
    }

    /**
     * Store a newly created mail template in storage.
     */
    public function store(PredefinedMailTemplateRequest $request): JsonResponse
    {
        $template = PredefinedMailTemplate::create(
            $request->merge(['user_id' => $request->user()->id])->all()
        );

        return $this->response(
            new PredefinedMailTemplateResource($template->load('user')),
            201
        );
    }

    /**
     * Update the specified mail template in storage.
     */
    public function update(string $id, PredefinedMailTemplateRequest $request): JsonResponse
    {
        $template = PredefinedMailTemplate::findOrFail($id);

        $this->authorize('update', $template);

        $template->fill($request->except('user_id'))->save();

        return $this->response(
            new PredefinedMailTemplateResource($template->load('user'))
        );
    }

    /**
     * Remove the specified mail template from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $template = PredefinedMailTemplate::findOrFail($id);

        $this->authorize('delete', $template);

        $template->delete();

        return $this->response('', 204);
    }
}
