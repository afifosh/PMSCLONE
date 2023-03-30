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
use App\Http\Resources\EmailAccountResource;
use App\Contracts\Repositories\EmailAccountRepository;

class SharedEmailAccountController extends Controller
{
    /**
     * Initialize new SharedEmailAccountController instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     */
    public function __construct(protected EmailAccountRepository $repository)
    {
    }

    /**
     * Display shared email accounts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke()
    {
        $accounts = $this->repository->withResponseRelations()->getShared();

        return $this->response(
            EmailAccountResource::collection($accounts)
        );
    }
}
