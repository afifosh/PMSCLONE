<?php

namespace App\Http\Controllers\Admin\MailClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\MailClient\Http\Requests\EmailAccountRequest;
use Modules\MailClient\Http\Resources\EmailAccountResource;
use Modules\MailClient\Models\EmailAccount;
use Modules\MailClient\Services\EmailAccountService;
use Modules\MailClient\Criteria\EmailAccountsForUserCriteria;

class EmailAccountController extends Controller
{
  /**
   * Get all email accounts the user can access.
   */
  public function index()
  {
    $accounts = EmailAccount::withResponseRelations()
      ->criteria(EmailAccountsForUserCriteria::class)
      ->get();

    if (request()->route()->getName() == 'admin.mail.accounts.manage')
      return view('admin.pages.emails.manage-accounts', compact('accounts'));

    return view('admin.pages.emails.index', compact('accounts'));
  }

  /**
   * Display email account.
   */
  public function show(string $id): JsonResponse
  {
    $account = EmailAccount::withResponseRelations()->findOrFail($id);

    $this->authorize('view', $account);

    return $this->response(new EmailAccountResource($account));
  }

  public function edit($id)
  {
    $account = EmailAccount::withResponseRelations()->findOrFail($id);

    return view('admin.pages.emails.partials.edit-account', compact('account'))->render();
  }

  /**
   * Store a newly created email account in storage.
   */
  public function store(EmailAccountRequest $request, EmailAccountService $service): JsonResponse
  {
    $model = $service->create($request->all());

    $account = EmailAccount::withResponseRelations()->find($model->id);

    $account->wasRecentlyCreated = true;

    return $this->response(
      new EmailAccountResource($account),
      201
    );
  }

  /**
   * Update the specified account in storage.
   */
  public function update(string $id, EmailAccountRequest $request, EmailAccountService $service): JsonResponse
  {
    $this->authorize('update', $account = EmailAccount::find($id));

    // The user is not allowed to update these fields after creation
    $except = ['email', 'connection_type', 'user_id', 'initial_sync_from'];

    $service->update($account, $request->except($except));

    return $this->response(
      new EmailAccountResource(EmailAccount::withResponseRelations()->find($account->id))
    );
  }

  /**
   * Remove the specified account from storage.
   */
  public function destroy(string $id, Request $request): JsonResponse
  {
    $this->authorize('delete', $account = EmailAccount::findOrFail($id));

    $account->delete();

    return $this->response([
      'unread_count' => EmailAccount::countUnreadMessagesForUser($request->user()),
    ]);
  }

  /**
   * Get all shared accounts unread messages.
   */
  public function unread(Request $request): int
  {
    return EmailAccount::countUnreadMessagesForUser($request->user());
  }
}
