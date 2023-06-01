<?php

namespace App\Http\Controllers\Admin\MailClient;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Module;
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




    private function getAccount($id){
       return EmailAccount::withResponseRelations()->findOrFail($id);
    }

    public function share($id)
    {
         $users=Admin::where('id', '!=', auth()->user()->id)->get();
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

}
