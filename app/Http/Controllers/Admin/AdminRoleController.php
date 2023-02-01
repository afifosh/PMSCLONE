<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Throwable;

class AdminRoleController extends Controller
{
    public function index()
    {
        $data['roles'] = Role::where('guard_name', 'admin')->withCount('users')->get();

        return view('admin.roles', $data);
    }

    public function create()
    {
      $modules = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'admin');
      })->with('permissions')->get();
      $view = view('admin._partials.sections.add-role', compact('modules'))->render();

      return $view;
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
      $role = Role::where('guard_name', 'admin')->where('id', $role)->with('permissions')->firstOrFail();
      $modules = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'admin');
      })->with('permissions')->get();
      $view = view('admin._partials.sections.edit-role', compact('modules', 'role'))->render();

      return $view;
    }
}
