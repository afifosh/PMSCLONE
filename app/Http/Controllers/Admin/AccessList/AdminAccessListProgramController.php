<?php

namespace App\Http\Controllers\Admin\AccessList;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Program;
use Illuminate\Http\Request;
use DataTables;

class AdminAccessListProgramController extends Controller
{
  public function index($admin_id)
  {
    return DataTables::of(Program::accessibleByAdmin($admin_id))
      ->addColumn('granted_till', function ($program) {
        return date('d M, Y', strtotime($program->pivotAccessLists[0]->granted_till));
      })
      ->addColumn('actions', function ($program)  use ($admin_id) {
        return view('admin.pages.access-lists.programs.action', compact('program', 'admin_id'));
      })
      ->make(true);
  }

  public function destroy(Admin $adminAccessList, $program)
  {
    $adminAccessList->auditDetach('accessiblePrograms', $program);

    return $this->sendRes('Program removed successfully.', ['event' => 'table_reload', 'table_id' => 'programs-child-table-' . $adminAccessList->id]);
  }
}
