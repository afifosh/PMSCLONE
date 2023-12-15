<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contract;
use App\Models\ContractNotifiableUser;
use App\Services\Core\Setting\SettingService;
use Illuminate\Http\Request;

class NotifiableUserController extends Controller
{
  public function __construct()
  {
    $this->middleware('permission:update contract')->only(['create', 'store']);
    $this->middleware('permission:delete contract')->only(['destroy']);
  }

  public function create(Contract $contract)
  {
    $config = (new SettingService())->getFormattedSettings('contract-notifications');
    $users = $contract->notifiableUsers()->pluck('admin_id')->toArray();
    $users = array_merge($users, explode(',', $config['emails'] ?? ''));
    $users = $users ? $users : [];
    $data['admins'] = Admin::whereNotIn('id', $users)->get();
    $data['contract'] = $contract;
    $data['model'] = new ContractNotifiableUser();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.settings.notifiable-users.create', $data)->render()]);
  }

  public function store(Contract $contract, Request $request)
  {
    $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
    ],[
      'users.required' => 'Please select at least one user',
      'users.*.required' => 'Please select at least one user',
    ]);

    $contract->notifiableUsers()->syncWithoutDetaching(filterInputIds($request->users));

    return $this->sendRes('User(s) Added Successfully', ['event' => 'table_reload', 'table_id' => 'contract-notifiable-users-table', 'close' => 'globalModal']);
  }

  public function destroy(Contract $contract, Admin $notifiableUser)
  {
    $contract->notifiableUsers()->detach($notifiableUser->id);

    return $this->sendRes('User Removed Successfully', ['event' => 'table_reload', 'table_id' => 'contract-notifiable-users-table']);
  }
}
