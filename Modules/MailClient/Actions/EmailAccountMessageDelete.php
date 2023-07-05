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

namespace Modules\MailClient\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Modules\Core\Actions\ActionFields;
use Modules\Core\Actions\ActionRequest;
use Modules\Core\Actions\DestroyableAction;
use Modules\MailClient\Http\Resources\EmailAccountResource;
use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Services\EmailAccountMessageSyncService;

class EmailAccountMessageDelete extends DestroyableAction
{
    /**
     * Handle method.
     *
     * @return mixed
     */
    public function handle(Collection $models, ActionFields $fields)
    {
        $accountId = request()->integer('account_id');

        $service = new EmailAccountMessageSyncService();

        $service->delete($models, request()->folder_id);

        $account = EmailAccount::withResponseRelations()->find($accountId);

        return [
            'unread_count' => EmailAccount::countUnreadMessagesForUser(auth()->user()),
            'account' => new EmailAccountResource($account),
            'trash_folder_id' => $account->trashFolder->id,
        ];
    }

    /**
     * @param  \Illumindate\Database\Eloquent\Model  $model
     */
    public function authorizedToRun(ActionRequest $request, $model): bool
    {
        return $request->user()->can('view', $model->account);
    }

    /**
     * Query the models for execution
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function findModelsForExecution($ids, Builder $query)
    {
        return $query->with('account.user')->findMany($ids);
    }

    /**
     * Action name.
     */
    public function name(): string
    {
        return __('core::app.delete');
    }
}