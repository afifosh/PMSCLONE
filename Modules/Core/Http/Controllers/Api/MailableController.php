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
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Http\Resources\MailableResource;
use Modules\Core\Models\MailableTemplate;

class MailableController extends ApiController
{
    /**
     * Retrieve all mail templates.
     */
    public function index(): JsonResponse
    {
        $collection = MailableResource::collection(MailableTemplate::orderBy('name')->get());

        return $this->response($collection);
    }

    /**
     * Retrieve mail templates in specific locale.
     */
    public function forLocale(string $locale): JsonResponse
    {
        $collection = MailableResource::collection(MailableTemplate::orderBy('name')->forLocale($locale)->get());

        return $this->response($collection);
    }

    /**
     * Display the specified resource.
     */
    public function show(MailableTemplate $template): JsonResponse
    {
        return $this->response(new MailableResource($template));
    }

    /**
     * Update the specified mail template in storage.
     */
    public function update(MailableTemplate $template, Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:191',
            'html_template' => 'required|string',
        ]);

        $template->fill($request->all());
        $template->save();

        return $this->response(new MailableResource($template));
    }
}
