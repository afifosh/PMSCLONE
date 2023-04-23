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

use App\Innoclapps\Actions\Action;
use Illuminate\Support\Collection;
use App\Http\Requests\ActionRequest;
use App\Innoclapps\Actions\ActionFields;
use App\Http\Resources\EmailAccountResource;
use App\Contracts\Repositories\EmailAccountRepository;
use App\Contracts\Repositories\EmailAccountMessageRepository;

class EmailAccountMessageMarkAsUnread extends Action
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

        $repository         = resolve(EmailAccountMessageRepository::class);
        $accountsRepository = resolve(EmailAccountRepository::class);
        $repository->batchMarkAsUnread($models, $accountId, request()->folder_id);

        return [
            'unread_count' => resolve(EmailAccountRepository::class)->countUnreadMessagesForUser(auth()->user()),
            'account'      => new EmailAccountResource($accountsRepository->withResponseRelations()->find($accountId)),
        ];
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
        return __('mail.mark_as_unread');
    }
}
