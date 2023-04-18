<?php

namespace App\Http\Controllers\Admin\EmailAccount;

use App\Contracts\Repositories\EmailAccountRepository;
use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailAccountRequest;
use App\Models\EmailAccount;
use Illuminate\Http\Request;

class EmailAccountController extends Controller
{
     /**
     * Initialize new EmailAccountController instance.
     *
     * @param \App\Contracts\Repositories\EmailAccountRepository $repository
     */
    public function __construct(protected EmailAccountRepository $repository)
    {
    }

    public function index(Request $request)
    {
        // $accounts = EmailAccount::where('user_id',\Auth::user()->id)->get();
        $accounts = $this->repository->withResponseRelations()
        ->pushCriteria(new EmailAccountsForUserCriteria(\Auth::user()))
        ->all();

        return view('admin.pages.emails.index',compact('accounts'));
    }

    public function manageAccounts(Request $request)
    {
        // $accounts = EmailAccount::where('user_id',\Auth::user()->id)->get();
        $accounts = $this->repository->withResponseRelations()
        ->pushCriteria(new EmailAccountsForUserCriteria(\Auth::user()))
        ->all();

        return view('admin.pages.emails.manage-accounts',compact('accounts'));
    }

    /**
     * Display email account;
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
         $account = $this->repository->withResponseRelations()->find($id);
         return response()->json($account);
    }


    public function edit($id)
    {
         $account = $this->repository->withResponseRelations()->find($id);

         return view('admin.pages.emails.partials.edit-account',compact('account'))->render();
    }


    /**
     * Store a newly created email account in storage
     *
     * @param \App\Http\Requests\EmailAccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(EmailAccountRequest $request)
    {
        $model = $this->repository->create($request->all());

        $account = $this->repository->withResponseRelations()->find($model->id);

        $account->wasRecentlyCreated = true;

        return response(
            "Account successfully created.",
            201
        );
    }

    /**
     * Update the specified account in storage
     *
     * @param int $id
     * @param \App\Http\Requests\EmailAccountRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, EmailAccountRequest $request)
    {
        // // The user is not allowed to update these fields after creation
         $except = ['email', 'connection_type', 'user_id', 'initial_sync_from'];
         $account = $this->repository->update($request->except($except), $id);

         return response("Account updated successfully.");
    }

    /**
     * Remove the specified account from storage
     *
     * @param int $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
         $this->repository->delete($id);

        return response("Account deleted successfully.");
    }

    public function unread()
    {
         return $this->repository->countUnreadMessagesForUser(\Auth::user());
    }
}
