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
use Modules\MailClient\Models\EmailAccount;

class EmailAccountPrimaryStateController extends ApiController
{
    /**
     * Mark the given account as primary for the current user.
     */
    public function update(string $id): JsonResponse
    {
        $this->authorize('view', $account = EmailAccount::findOrFail($id));

        $account->markAsPrimary(auth()->user());

        return $this->response('', 204);
    }

    /**
     * Remove primary account for the current user.
     */
    public function destroy(): JsonResponse
    {
        EmailAccount::unmarkAsPrimary(auth()->user());

        return $this->response('', 204);
    }
}