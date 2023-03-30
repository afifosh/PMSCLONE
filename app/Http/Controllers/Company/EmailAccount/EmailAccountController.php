<?php

namespace App\Http\Controllers\Company\EmailAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailAccountRequest;
use App\Models\EmailAccount;

class EmailAccountController extends Controller
{
 
    public function __construct()
    {
    }

    public function index()
    {
        $accounts = EmailAccount::where('user_id',\Auth::user()->id)->get();

        return view('pages.emails.index',compact('accounts'));
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
        // $account = $this->repository->withResponseRelations()->find($id);

        // $this->authorize('view', $account);

        // return $this->response(new EmailAccountResource($account));
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
        // $model = $this->repository->create($request->all());

        // $account = $this->repository->withResponseRelations()->find($model->id);

        // $account->wasRecentlyCreated = true;

        // return $this->response(
        //     new EmailAccountResource($account),
        //     201
        // );
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
        // $this->authorize('update', $this->repository->find($id));

        // // The user is not allowed to update these fields after creation
        // $except = ['email', 'connection_type', 'user_id', 'initial_sync_from'];

        // $account = $this->repository->update($request->except($except), $id);

        // return $this->response(
        //     new EmailAccountResource($this->repository->withResponseRelations()->find($account->id))
        // );
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
        // $this->authorize('delete', $this->repository->find($id));

        // $this->repository->delete($id);

        // return $this->response([
        //     'unread_count' => $this->repository->countUnreadMessagesForUser($request->user()),
        // ]);
    }

    public function unread()
    {
        // return $this->repository->countUnreadMessagesForUser($request->user());
    }
}
