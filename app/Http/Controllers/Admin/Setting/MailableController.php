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

namespace App\Http\Controllers\Admin\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\MailableResource;
use App\Innoclapps\Contracts\Repositories\MailableRepository;

class MailableController extends Controller
{
    /**
     * Initialize new MailableController instance.
     *
     * @param \App\Innoclapps\Contracts\Repositories\MailableRepository $repository
     */
    public function __construct(protected MailableRepository $repository)
    {
    }

    /**
     * Retrieve all mail templates
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $templates=MailableResource::collection($this->repository->orderBy('name')->all());
        return view('admin.pages.settings.email-templates.index',compact('templates'));
    }

    /**
     * Retrieve mail templates in specific locale
     *
     * @param string $locale
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forLocale($locale)
    {
        return response(
            MailableResource::collection($this->repository->orderBy('name')->forLocale($locale))
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return response(new MailableResource(
            $this->repository->find($id)
        ));
    }

    /**
     * Update the specified mail template in storage
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $request->validate([
            'subject'       => 'required|string|max:191',
            'html_template' => 'required|string',
        ]);

        $template = $this->repository->update($request->all(), $id);

        return response(new MailableResource($template));
    }
}
