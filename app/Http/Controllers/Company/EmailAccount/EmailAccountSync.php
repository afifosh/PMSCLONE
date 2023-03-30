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
use Illuminate\Support\Facades\Artisan;
use App\Http\Resources\EmailAccountResource;
use App\Contracts\Repositories\EmailAccountRepository;

class EmailAccountSync extends Controller
{
    /**
     * Initialize new EmailAccountSync instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     */
    public function __construct(protected EmailAccountRepository $repository)
    {
    }

    /**
     * Synchronize email account
     *
     * @param int $accountId
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \App\MailClient\Exceptions\SynchronizationInProgressException
     */
    public function __invoke($accountId)
    {
        $this->authorize('view', $this->repository->find($accountId));

        Artisan::call('concord:sync-email-accounts', [
            '--account'   => $accountId,
            '--broadcast' => false,
            '--manual'    => true,
        ]);

        return $this->response(
            new EmailAccountResource(
                $this->repository->withResponseRelations()->find($accountId)
            )
        );
    }
}
