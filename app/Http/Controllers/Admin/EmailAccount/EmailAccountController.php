<?php

namespace App\Http\Controllers\Admin\EmailAccount;

use App\Contracts\Repositories\EmailAccountRepository;
use App\Criteria\EmailAccount\EmailAccountsForUserCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmailAccountRequest;
use App\Models\Admin;
use App\Models\EmailAccount;
use App\Models\Module;
use App\Models\UserEmailAccount;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

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
        $accounts = $this->getAccounts();
        return view('admin.pages.emails.index',compact('accounts'));
    }
    private function getAccounts(){
       return EmailAccount::whereNull('user_id')->orWhere('created_by',\Auth::user()->id)->get();
    }
    public function manageAccounts(Request $request)
    {
        $accounts = $this->getAccounts();
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
         $account = $this->getAccount($id);
         $permission=auth()->user()->getPermission($account);
         $data = array(
            'account' => $account,
            'permission' => $permission
        );
          return response()->json($data);
    }

    private function getAccount($id){
       return $this->repository->withResponseRelations()->find($id);
    }

    public function edit($id)
    {
         $account = $this->getAccount($id);

         return view('admin.pages.emails.partials.edit-account',compact('account'))->render();
    }

    public function share($id)
    {
         $users=Admin::all();
         $module = Module::where('name','=','Mailbox')->whereHas('permissions', function ($q) {
            $q->where('guard_name', 'admin');
          })->with('permissions')->first();
         $account = $this->getAccount($id);
         return view('admin.pages.emails.partials.shared-account-access',compact('account','users','module'))->render();
    }

    public function setPermission($id, Request $request){
            $this->createPermission($id,$request);
            return response(
            "Permission successfully saved."
        );
    }

    private function createPermission($id,$request){
        // $module = Module::where('name','=','Mailbox')->whereHas('permissions', function ($q) {
        //     $q->where('guard_name', 'admin');
        //   })->with('permissions')->first();
        $user=Admin::find($request->user_id);
        if($request->permission_id!=0){
            $user->emailAccounts()->syncWithoutDetaching([
                $id=>[
                    'permission_id'=>$request->permission_id
                ]
            ]);
        }
        else{
            $user->emailAccounts()->detach([
                $id
            ]);

        }
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

        return response("Account successfully created.");
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
