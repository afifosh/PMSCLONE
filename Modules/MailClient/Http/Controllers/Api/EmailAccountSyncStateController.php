<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\ApiController;
use Modules\Core\Synchronization\SyncState;
use Modules\MailClient\Http\Resources\EmailAccountResource;
use Modules\MailClient\Models\EmailAccount;

class EmailAccountSyncStateController extends ApiController
{
    /**
     * Enable synchronization for the given email account.
     */
    public function enable(string $id): JsonResponse
    {
        $account = EmailAccount::withCommon()->findOrFail($id);

        $this->authorize('update', $account);

        if ($account->isSyncStoppedBySystem()) {
            abort(403, 'Synchronization for this account is stopped by system. ['.$account->sync_state_comment.']');
        }

        $account->enableSync();

        return $this->response(
            new EmailAccountResource($account)
        );
    }

    /**
     * Disable synchronization for the given email account.
     */
    public function disable(string $id): JsonResponse
    {
        $account = EmailAccount::withCommon()->findOrFail($id);

        $this->authorize('update', $account);

        $account->setSyncState(
            SyncState::DISABLED,
            'Account synchronization disabled by user.'
        );

        return $this->response(
            new EmailAccountResource($account)
        );
    }
}
