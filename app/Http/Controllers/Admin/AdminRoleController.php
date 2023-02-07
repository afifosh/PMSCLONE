<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\AdminsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

class AdminRoleController extends Controller
{
    function __construct()
    {
      $this->middleware('permission:read role|create role|update role|delete role|read user', ['only' => ['index', 'show']]);
      $this->middleware('permission:create role', ['only' => ['create', 'store']]);
      $this->middleware('permission:update role', ['only' => ['edit', 'update']]);
      $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index(AdminsDataTable $dataTable)
    {
        $data['roles'] = Role::where('guard_name', 'admin')->with('users')->withCount('users')->get();
        return $dataTable->render('admin.pages.roles.index', $data);
        // return view('admin.pages.roles.index', $data);
    }

    public function create()
    {
      $modules = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'admin');
      })->with('permissions')->get();
      return view('admin._partials.sections.add-role', compact('modules'))->render();
    }

    public function store(Request $request)
    {
      $request->validate([
        'role' => 'required|string|max:255',
        'permissions' => 'array',
      ]);
      try{
        $role = Role::create(['name' => $request->role, 'guard_name' => 'admin']);
        $role->syncPermissions($request->permissions);

        return back()->with('success', 'Role Created Successfully');
      }catch(Throwable $e){
        return back()->with('error', $e->getMessage());
      }
    }

    public function edit($role)
    {
      $data['role'] = Role::where('guard_name', 'admin')->where('id', $role)->firstOrFail();
      $data['allowed_permissions'] = $data['role']->permissions()->pluck('id')->toArray();
      $data['modules'] = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'admin');
      })->with('permissions')->get();

      return view('admin._partials.sections.edit-role', $data)->render();
    }

    public function update(Role $role, Request $request)
    {
      $role->update(['name' => $request->role]);
      $role->syncPermissions($request->permissions);
      return back()->with('success', 'Role Updated Successfully');
    }
}
