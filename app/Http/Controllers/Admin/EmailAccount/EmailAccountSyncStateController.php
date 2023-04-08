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

namespace App\Http\Controllers\Admin\EmailAccount;

use App\Enums\SyncState;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailAccountResource;
use App\Contracts\Repositories\EmailAccountRepository;

class EmailAccountSyncStateController extends Controller
{
    /**
     * Initialize new EmailAccountSyncStateController instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     */
    public function __construct(protected EmailAccountRepository $repository)
    {
    }

    /**
     * Enable synchronization for email account
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable($id)
    {
        $account = $this->repository->find($id);

        if ($account->isSyncStoppedBySystem()) {
            abort(403, 'Synchronization for this account is stopped by system. [' . $account->sync_state_comment . ']');
        }

        $this->repository->enableSync((int) $account->id);

        return response("Sync enabled.");

    }

    /**
     * Enable synchronization for email account
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable($id)
    {
        $account = $this->repository->find($id);

        $this->repository->setSyncState(
            $account->id,
            SyncState::DISABLED,
            'Account synchronization disabled by user.'
        );

        return response("Sync disabled.");
    }
}
