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
        // $accounts = EmailAccount::where('user_id',\Auth::user()->id)->get();
        $accounts = $this->repository->withResponseRelations()
        ->pushCriteria(new EmailAccountsForUserCriteria(\Auth::user()))
        ->all();

        return view('admin.pages.emails.index',compact('accounts'));
    }

    public function manageAccounts(Request $request)
    {
        $accounts = EmailAccount::where('created_by',\Auth::user()->id)->get();
        // $accounts = $this->repository->withResponseRelations()
        // ->where(new EmailAccountsForUserCriteria(\Auth::user(),'created_by'))
        // ->all();

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

    public function share($id)
    {
         $users=Admin::all();
         $module = Module::where('name','=','Mailbox')->whereHas('permissions', function ($q) {
            $q->where('guard_name', 'admin');
          })->with('permissions')->first();
         $account = $this->repository->withResponseRelations()->find($id);
         return view('admin.pages.emails.partials.shared-account-access',compact('account','users','module'))->render();
    }

    public function setPermission($id, Request $request){
            $this->createPermission($id,$request);
            return response(
            "Permission successfully saved."
        );
    }

    private function createPermission($id,$request){
        $module = Module::where('name','=','Mailbox')->whereHas('permissions', function ($q) {
            $q->where('guard_name', 'admin');
          })->with('permissions')->first();
        //   foreach($module->permissions as $permission){
        //     UserEmailAccount::where([['user_id', '=', $request->user_id],
        //     ['permission_id', '=', $permission->id],
        //     ['email_account_id', '=', $id]
        //   ])->delete();
        //   }
          if($request->permission_id!=0){
            $user=Admin::find($request->user_id);
            $emailAccountA = EmailAccount::find($id);
            // $emailAccountB = EmailAccount::find(5);

            // $user->givePermissionTo($request->permission_id, $emailAccountA);

            // if($user->can('full access',$emailAccountA)){
            //     \Log::info('have permission');
            // }
            // if($user->can('full access',$emailAccountB)){
            //     \Log::info("message");
            // }
            // $user->emailAccounts()->attach(
            //     $emailAccountA , [
            //         'permission_id' => $request->permission_id,
            //         'user_id'=>$user->id,
            //     ]);
            $user_email_account=new UserEmailAccount();
          $user_email_account->user_id=$request->user_id;
          $user_email_account->permission_id=$request->permission_id;
          $user_email_account->email_account_id=$id;
          $user_email_account->save();
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
