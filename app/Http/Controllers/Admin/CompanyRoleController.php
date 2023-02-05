<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use Illuminate\Http\Request;
use Throwable;

class CompanyRoleController extends Controller
{

    function __construct()
    {
      $this->middleware('permission:read company role|create company role|update company role|delete company role', ['only' => ['index', 'show']]);
      $this->middleware('permission:create company role', ['only' => ['create', 'store']]);
      $this->middleware('permission:update company role', ['only' => ['edit', 'update']]);
      $this->middleware('permission:delete company role', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      $role = new Role(['guard_name' => 'web']);

      $data['roles'] = $role->where('guard_name', 'web')->withCount('users')->get();

      return view('admin.pages.company-roles.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $modules = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'web');
      })->with('permissions')->get();
      $view = view('admin._partials.sections.add-company-role', compact('modules'))->render();

      return $view;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $request->validate([
        'role' => 'required|string|max:255',
        'permissions' => 'array',
      ]);


      try{
        $role = Role::create(['name' => $request->role, 'guard_name' => 'web']);
        $role->syncPermissions($request->permissions);

        return back()->with('success', 'Role Created Successfully');
      }catch(Throwable $e){
        return back()->with('error', $e->getMessage());
      }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit($role)
    {
      $data['role'] = Role::where('guard_name', 'web')->where('id', $role)->firstOrFail();
      $data['allowed_permissions'] = $data['role']->permissions()->pluck('id')->toArray();
      $data['modules'] = Module::whereHas('permissions', function($q){
        $q->where('guard_name', 'web');
      })->with('permissions')->get();
      $view = view('admin._partials.sections.edit-company-role', $data)->render();

      return $view;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $role)
    {
      $request->validate([
        'role' => 'required|max:255|string',
      ]);
      $model = new Role(['guard_name' => 'web']);
      $role = $model->where('id', $role)->firstOrFail();
      $role->update(['name' => $request->role]);
      $role->syncPermissions($request->permissions);
      return back()->with('success', 'Role Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        //
    }
}
