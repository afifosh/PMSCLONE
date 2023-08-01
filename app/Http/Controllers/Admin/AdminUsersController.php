<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AdminsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CompanyDepartment;
use App\Models\CompanyDesignation;
use App\Models\PartnerCompany;
use App\Models\Role;
use App\Notifications\Admin\WelcomeNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Events\TwoFactorCodeEvent;
Use \Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Session;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\ConfirmPassword;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\FailedTwoFactorLoginResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;
use App\Traits\TwoFactorAuthentication;

class AdminUsersController extends Controller
{

  use TwoFactorAuthentication;

  function __construct()
  {
    $this->middleware('permission:read user|create user|update user|delete user', ['only' => ['index', 'show']]);
    $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete user', ['only' => ['destroy']]);
    $this->middleware('permission:impersonate user', ['only' => ['impersonate']]);
  }

  public function index(AdminsDataTable $dataTable)
  {
    // $data['roles'] = Role::where('guard_name', 'admin')->with('users')->withCount('users')->get();
    $data['partners'] = PartnerCompany::distinct()->pluck('name', 'id');
    $data['roles'] = Role::where('guard_name', 'admin')->distinct()->pluck('name');
    $data['statuses'] = Admin::distinct()->pluck('status');
    return $dataTable->render('admin.pages.partner.employees.index', $data);
        // return view('admin.pages.partner.employees.index', $data);
  }

  public function create()
  {
    $data['user'] = new Admin();
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend(__('Select Organization'), '');
    $data['departments'] = ['' => 'Select Department'];
    $data['designations'] = ['' => 'Select Designation'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', 'unique:admins,email'],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id',
      'email_verified_at' => 'sometimes',
    ],[
      'company_id.required' => __('Company field is required'),
      'department_id.required' => __('Department field is required'),
      'designation_id.required' => __('Designation field is required'),
    ]);
    unset($att['roles']);
    // if ($request->password) {
    //   $att['password'] = Hash::make($att['password']);
    // } else {
    //   unset($att['password']);
    // }
    $password = Str::random(15);
    $att['password'] = Hash::make($password);
    $att['email_verified_at'] = $request->boolean('email_verified_at') ? now() : null;
    $user = Admin::create($att);
    $user->notify(new WelcomeNotification($password));
    $user->syncRoles([$request->roles]);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
  }

  public function show(Admin $user)
  {
    return view('admin.pages.roles.admins.show', compact('user'));
  }

  public function edit(Admin $user)
  {
    $data['user'] = $user;
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend('Select Organization', '');
    $data['departments'] = CompanyDepartment::where('id', @$user->designation->department_id)->pluck('name', 'id')->prepend('Select Department', '');
    $data['designations'] = CompanyDesignation::where('id', $user->designation_id)->pluck('name', 'id')->prepend('Select Designation', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  public function editPassword(Admin $user)
  {
    $data['user'] = $user;
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit-password', $data)->render()]);
  }

  public function updatePassword(Request $request, Admin $user)
  {
    $request->validate([
      'password' => 'required|min:8|max:255|confirmed',
    ]);
    $user->update(['password' => Hash::make($request->password)]);
    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
  }

  public function update(Request $request, Admin $user)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', Rule::unique('admins')->ignore($user->id),],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id',
      'email_verified_at' => 'sometimes'
    ]);
    unset($att['roles']);
    if($user->email_verified_at && $request->boolean('email_verified_at')){
      unset($att['email_verified_at']);
    }else if(!$user->email_verified_at && $request->boolean('email_verified_at')){
      $att['email_verified_at'] = now();
    }else if(!$request->boolean('email_verified_at')){
      $att['email_verified_at'] = null;
    }
    $user->syncRoles([$request->roles]);
    if ($user->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
    }
  }

  public function destroy(Admin $user)
  {
    if ($user->id == 1)
      return $this->sendError('This User Cannot be deleted');
    if ($user->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table']);
    }
  }

  public function impersonate(Admin $user)
  {
    auth('admin')->user()->impersonate($user, 'admin');

    return back()->with('success', 'impersonated');
  }

  public function leaveImpersonate()
  {
    auth('admin')->user()->leaveImpersonation();

    return back()->with('success', 'Impersonation Removed');
  }

}
