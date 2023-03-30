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

namespace App\Http\Controllers\EmailAccount;

use App\Http\Controllers\Controller;
use App\Contracts\Repositories\EmailAccountRepository;

class EmailAccountPrimaryStateController extends Controller
{
    /**
     * Initialize new EmailAccountPrimaryStateController instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     */
    public function __construct(protected EmailAccountRepository $repository)
    {
    }

    /**
     * Mark the given account as primary
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id)
    {
        $this->authorize('view', $account = $this->repository->find($id));

        $this->repository->markAsPrimary($account, auth()->user());

        return $this->response('', 204);
    }

    /**
     * Remove primary account
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        $this->repository->removePrimary(auth()->user());

        return $this->response('', 204);
    }
}
