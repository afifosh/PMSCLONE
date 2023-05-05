<?php

namespace App\Http\Controllers\Company;

use App\DataTables\Company\UsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(UsersDataTable $dataTable)
  {
    $data['roles'] = Role::where('guard_name', 'web')->withCount('users')->get();
    return $dataTable->render('pages.users.index', $data);
    // view('pages.users.index');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $data['user'] = new User();
    $data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('pages.users.edit', $data)->render()]);
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
      'email' => ['required', 'string', 'max:255', 'unique:users,email'],
      'password' => 'required|confirmed',
      'status' => 'required',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
    ]);
    unset($att['roles']);
    if($request->password){
      $att['password'] = Hash::make($att['password']);
    }else{
      unset($att['password']);
    }
    $user = User::create($att);
    $user->syncRoles(array_unique($request->roles));
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID, 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function show(User $user)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function edit(User $user)
  {
    $data['user'] = $user;
    $data['roles'] = Role::where('guard_name', 'web')->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('pages.users.edit', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, User $user)
  {
    $att = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'phone' => 'required|string|max:255',
      'email' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id),],
      'status' => 'required',
      'password' => 'sometimes|confirmed',
      'roles' => 'required|array',
      'roles.*' => 'exists:roles,id',
    ]);
    unset($att['roles']);
    if($request->password){
      $att['password'] = Hash::make($att['password']);
    }else{
      unset($att['password']);
    }
    $user->syncRoles(array_unique($request->roles));
    if ($user->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID, 'close' => 'globalModal']);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\User  $user
   * @return \Illuminate\Http\Response
   */
  public function destroy(User $user)
  {
    if ($user->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => User::DT_ID]);
    }
  }

  public function showRole($role)
  {
    $data['role'] = Role::where('guard_name', 'web')->where('id', $role)->firstOrFail();
      $data['allowed_permissions'] = $data['role']->permissions()->pluck('id')->toArray();
      $data['modules'] = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'web');
      })->with('permissions')->get();
      $data['true_all'] = $data['role']->name == Role::COMPANY_ADMIN_ROLE;
      return view('pages.users.role-details', $data)->render();
  }
}
