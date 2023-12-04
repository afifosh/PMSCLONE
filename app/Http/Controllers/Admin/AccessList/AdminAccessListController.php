<?php

namespace App\Http\Controllers\Admin\AccessList;

use App\DataTables\Admin\AccessList\AdminAccessListsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAccessList;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAccessListController extends Controller
{
  public function index(AdminAccessListsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.access-lists.index');
    // view('admin.pages.access-lists.index');
  }

  public function create()
  {
    $data['users'] = Admin::whereDoesntHave('accessiblePrograms')->get();
    $data['adminAccessList'] = new AdminAccessList;

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.access-lists.create', $data)->render(),
      'JsMethods' => ['initACLCreateTreeSelect'],
      'JsMethodsParams' => [
        json_encode([
          'programs_tree' => Program::tree()->select(['id', 'id as value', 'name', 'parent_id', 'depth', 'path'])->get()->toTree(),
          'selected_programs' => []
        ])
      ]
    ]);
  }

  public function store(Request $request)
  {
    // convert accessible_programs to array
    $validated = $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'granted_till' => 'required|date',
      'accessible_programs' => 'required',
    ]);
    // convert and validate accessible_programs
    $request->merge(['accessible_programs' => explode(',', $validated['accessible_programs'])]);

    $validated = $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'granted_till' => 'required|date',
      'accessible_programs' => 'required|array',
      'accessible_programs.*' => 'required|exists:programs,id',
    ]);

    DB::beginTransaction();
    try {
      $users = Admin::whereDoesntHave('accessiblePrograms')->whereIn('id', $validated['users'])->get();

      foreach ($users as $user) {
        $user->auditAttach('accessiblePrograms', filterInputIds($validated['accessible_programs']), ['granted_till' => $validated['granted_till']]);
      }

      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();

      return $this->sendError($e->getMessage());
    }
    return $this->sendRes('Added Successfully', ['event' => 'table_reload', 'table_id' => 'admin-access-lists-table', 'close' => 'globalModal']);
  }

  public function edit($admin_id)
  {
    $data['users'] = Admin::whereHas('accessiblePrograms')->where('id', $admin_id)->get();
    $data['admin_id'] = $admin_id;
    $data['granted_till'] = $data['users'][0]->accessiblePrograms[0]->pivot->granted_till;

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.access-lists.create', $data)->render(),
      'JsMethods' => ['initACLCreateTreeSelect'],
      'JsMethodsParams' => [
        json_encode([
          'programs_tree' => Program::tree()->select(['id', 'id as value', 'name', 'parent_id', 'depth', 'path'])->get()->toTree(),
          'selected_programs' => $data['users'][0]->accessiblePrograms()->pluck('programs.id')->toArray()
        ])
      ]
    ]);
  }

  public function update($admin_id, Request $request)
  {
    // convert accessible_programs to array
    $validated = $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'granted_till' => 'required|date',
      'accessible_programs' => 'required',
    ]);
    // convert and validate accessible_programs
    $request->merge(['accessible_programs' => explode(',', $validated['accessible_programs'])]);

    $validated = $request->validate([
      'users' => 'required|array',
      'users.*' => 'required|exists:admins,id',
      'granted_till' => 'required|date',
      'accessible_programs' => 'required|array',
      'accessible_programs.*' => 'required|exists:programs,id',
    ]);

    DB::beginTransaction();

    try {
      $users = Admin::whereHas('accessiblePrograms')->whereIn('id', $validated['users'])->get();
      foreach ($users as $user) {
        $user->accessiblePrograms()->syncWithPivotValues(filterInputIds($validated['accessible_programs']), ['granted_till' => $validated['granted_till']]);
      }
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();

      return $this->sendError($e->getMessage());
    }

    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => 'admin-access-lists-table', 'close' => 'globalModal']);
  }

  public function destroy($admin_id)
  {
    DB::beginTransaction();

    try {
      $admin = Admin::whereHas('accessiblePrograms')->where('id', $admin_id)->first();
      $admin->auditDetach('accessiblePrograms');
      DB::commit();
    } catch (\Exception $e) {
      DB::rollback();

      return $this->sendError($e->getMessage());
    }

    return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'admin-access-lists-table']);
  }
}
