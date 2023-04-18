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

namespace App\Resources\Inbox\Actions;

use Illuminate\Support\Collection;
use App\Http\Requests\ActionRequest;
use App\Innoclapps\Actions\ActionFields;
use App\Http\Resources\EmailAccountResource;
use App\Innoclapps\Actions\DestroyableAction;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;

class EmailAccountMessageDelete extends DestroyableAction
{
    /**
     * Handle method
     *
     * @param \Illuminate\Support\Collection $models
     * @param \App\Innoclapps\Actions\ActionFields $fields
     *
     * @return mixed
     */
    public function handle(Collection $models, ActionFields $fields)
    {
        $accountId = request()->account_id;

        $repository        = app($this->repository());
        $accountRepository = app(EmailAccountRepository::class);
        $repository->deleteForAccount($models, request()->folder_id);

        $account = $accountRepository->withResponseRelations()->find($accountId);

        return [
            'unread_count'    => $accountRepository->countUnreadMessagesForUser(auth()->user()),
            'account'         => new EmailAccountResource($account),
            'trash_folder_id' => $account->trashFolder->id,
        ];
    }

    /**
     * Provide the models repository class name
     *
     * @return string
     */
    public function repository()
    {
        return EmailAccountMessageRepository::class;
    }

    /**
     * @param \App\Http\Requests\ActionRequest $request
     * @param \Illumindate\Database\Eloquent\Model $model
     *
     * @return bool
     */
    public function authorizedToRun(ActionRequest $request, $model)
    {
        return $request->user()->can('view', $model->account);
    }

    /**
     * Action name
     *
     * @return string
     */
    public function name() : string
    {
        return __('app.delete');
    }
}
