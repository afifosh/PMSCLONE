<?php

namespace App\Http\Controllers\Admin\AccessList;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccessList\AccessListStoreRequest;
use App\Models\Admin;
use App\Models\AdminAccessList;
use App\Models\Program;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class AdminAccessListProgramController extends Controller
{
  public function index($admin_id)
  {
    return DataTables::of(Program::accessibleByAdmin($admin_id))
      ->addColumn('granted_till', function ($program) {
        if (!$program->pivotAccessLists[0]->granted_till) {
          return '<span class="badge bg-label-success">Permanent</span>';
        }

        return date('d M, Y', strtotime($program->pivotAccessLists[0]->granted_till));
      })
      ->addColumn('status', function ($program) {
        if ($program->pivotAccessLists[0]->is_revoked) {
          return '<span class="badge bg-label-danger">Revoked</span>';
        } else if ($program->pivotAccessLists[0]->granted_till && $program->pivotAccessLists[0]->granted_till < date('Y-m-d')) {
          return '<span class="badge bg-label-warning">Expired</span>';
        } else {
          return '<span class="badge bg-label-success">Active</span>';
        }
      })
      ->addColumn('actions', function ($program)  use ($admin_id) {
        return view('admin.pages.access-lists.programs.action', compact('program', 'admin_id'));
      })
      ->rawColumns(['actions', 'status', 'granted_till'])
      ->make(true);
  }

  public function revoke(Admin $adminAccessList, $program)
  {
    DB::beginTransaction();

    try {
      $adminAccessList->accessiblePrograms()->updateExistingPivot($program, ['is_revoked' => 1]);

      DB::commit();

      return $this->sendRes('Access revoked successfully.', ['event' => 'table_reload', 'table_id' => 'programs-child-table-' . $adminAccessList->id]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function create(Admin $adminAccessList)
  {
    $data['user'] = $adminAccessList;

    $data['acl'] = new AdminAccessList;

    $disabledProgramIds = $adminAccessList->accessiblePrograms()->pluck('programs.id')->toArray();

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.access-lists.programs.create', $data)->render(),
      'JsMethods' => ['initProgramACLEditTreeSelect'],
      'JsMethodsParams' => [
        json_encode([
          'programs_tree' => Program::tree()->select(['id', 'id as value', 'name', 'parent_id', 'depth', 'path', DB::raw('CASE WHEN id IN (' . implode(',', $disabledProgramIds) . ') THEN true ELSE false END as disabled')])->get()->toTree(),
          'selected_programs' => []
        ])
      ]
    ]);
  }

  public function store(Admin $adminAccessList, AccessListStoreRequest $request)
  {
    DB::beginTransaction();

    try {
      $adminAccessList->auditAttach('accessiblePrograms', filterInputIds($request->accessible_programs), ['granted_till' => $request->granted_till]);

      DB::commit();

      return $this->sendRes('Progmram added successfully.', ['event' => 'table_reload', 'table_id' => 'programs-child-table-' . $adminAccessList->id, 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function edit(Admin $adminAccessList, $program_id)
  {
    $data['user'] = $adminAccessList;

    $program = Program::accessibleByAdmin($adminAccessList->id)->findOrFail($program_id);

    $data['acl'] = $program->pivotAccessLists[0];

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.access-lists.programs.create', $data)->render(),
      'JsMethods' => ['initProgramACLEditTreeSelect'],
      'JsMethodsParams' => [
        json_encode([
          'programs_tree' => Program::tree()->select(['id', 'id as value', 'name', 'parent_id', 'depth', 'path'])->get()->toTree(),
          'selected_programs' => $program->id
        ])
      ]
    ]);
  }

  public function update(Admin $adminAccessList, $program_id, Request $request)
  {

    $request->merge([
      'is_permanent_access' => $request->boolean('is_permanent_access'),
      'granted_till' => $request->boolean('is_permanent_access') ? null : $request->granted_till,
      'is_revoked' => $request->boolean('is_revoked'),
    ]);

    $request->validate([
      'granted_till' => 'nullable|date|after_or_equal:today',
      'is_permanent_access' => 'required|boolean',
      'is_revoked' => 'required|boolean',
    ]);

    $program = Program::accessibleByAdmin($adminAccessList->id)->findOrFail($program_id);

    DB::beginTransaction();

    try {
      $adminAccessList->accessiblePrograms()->updateExistingPivot($program->id, [
        'granted_till' => $request->granted_till,
        'is_revoked' => $request->is_revoked
      ]);

      DB::commit();

      return $this->sendRes('Access updated successfully.', ['event' => 'table_reload', 'table_id' => 'programs-child-table-' . $adminAccessList->id, 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }

  public function destroy(Admin $adminAccessList, $program)
  {
    DB::beginTransaction();

    try {
      $adminAccessList->auditDetach('accessiblePrograms', $program);

      DB::commit();

      return $this->sendRes('Program removed successfully.', ['event' => 'table_reload', 'table_id' => 'programs-child-table-' . $adminAccessList->id]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendErr($e->getMessage());
    }
  }
}
