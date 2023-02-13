<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AdminsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\CompanyDepartment;
use App\Models\CompanyDesignation;
use App\Models\PartnerCompany;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUsersController extends Controller
{

  function __construct()
  {
    $this->middleware('permission:read user|create user|update user|delete user', ['only' => ['index', 'show']]);
    $this->middleware('permission:create user', ['only' => ['create', 'store']]);
    $this->middleware('permission:update user', ['only' => ['edit', 'update']]);
    $this->middleware('permission:delete user', ['only' => ['destroy']]);
    $this->middleware('permission:impersonate user', ['only' => ['impersonate']]);
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(AdminsDataTable $dataTable)
  {
    $data['roles'] = Role::where('guard_name', 'admin')->with('users')->withCount('users')->get();
    return $dataTable->render('admin.pages.partner.employees.index', $data);
        // return view('admin.pages.partner.employees.index', $data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $data['user'] = new Admin();
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend(__('Select Organization'), '');
    $data['departments'] = ['' => 'Select Department'];
    $data['designations'] = ['' => 'Select Designation'];
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', 'unique:admins,email'],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id'
    ],[
      'company_id.required' => __('Company field is required'),
      'department_id.required' => __('Department field is required'),
      'designation_id.required' => __('Designation field is required'),
    ]);
    unset($att['roles']);
    if ($request->password) {
      $att['password'] = Hash::make($att['password']);
    } else {
      unset($att['password']);
    }
    $user = Admin::create($att);
    $user->syncRoles($request->roles);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function show(Admin $admin)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function edit(Admin $user)
  {
    $data['user'] = $user;
    $data['roles'] = Role::where('guard_name', 'admin')->pluck('name', 'id');
    $data['companies'] = PartnerCompany::pluck('name', 'id')->prepend('Select Organization', '');
    $data['departments'] = CompanyDepartment::where('id', @$user->designation->department_id)->pluck('name', 'id')->prepend('Select Department', '');
    $data['designations'] = CompanyDesignation::where('id', $user->designation_id)->pluck('name', 'id')->prepend('Select Designation', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.roles.admins.edit', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Admin $user)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', Rule::unique('admins')->ignore($user->id),],
      'password' => 'sometimes|confirmed',
      'status' => 'required',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
      'company_id' => 'required|exists:partner_companies,id',
      'department_id' => 'required|exists:company_departments,id',
      'designation_id' => 'required|exists:company_designations,id'
    ]);
    unset($att['roles']);
    if ($request->password) {
      $att['password'] = Hash::make($att['password']);
    } else {
      unset($att['password']);
    }
    $user->syncRoles($request->roles);
    if ($user->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table', 'close' => 'globalModal']);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Admin  $admin
   * @return \Illuminate\Http\Response
   */
  public function destroy(Admin $user)
  {
    if ($user->id == 1)
      return $this->sendError('This User Cannot be deleted');
    if ($user->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'admins-table']);
    }
  }

  public function impersonate(Admin $admin)
  {
    auth('admin')->user()->impersonate($admin, 'admin');

    return back()->with('success', 'impersonated');
  }

  public function leaveImpersonate()
  {
    auth('admin')->user()->leaveImpersonation();

    return back()->with('success', 'Impersonation Removed');
  }
}
