<?php

namespace App\Http\Controllers\Admin\Program;

use App\DataTables\Admin\ProgramUsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminAccessList;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramUserController extends Controller
{
  public function index(Program $program, ProgramUsersDataTable $dataTable)
  {
    $program = $program->where('id', $program->id)->mine()->firstOrFail();

    return $dataTable->render('admin.pages.programs.users.index', compact('program'));
    // return view('admin.pages.programs.users.index');
  }

  public function edit(Program $program, Admin $user)
  {
    $programUser = AdminAccessList::ofProgram($program->id)
      ->where('admin_id', $user->id)
      ->first();

    // Check if the ProgramUser relationship exists
    if (!$programUser) {
      // If not exist, handle accordingly, e.g., error message
      return $this->sendRes('error', ['message' => 'The specified relationship does not exist.']);
    }

    // Prepare data for the view
    $data['program'] = $program;
    $data['accessList'] = $programUser;

    // Instead of fetching and filtering all users, just get the specific user
    $data['users'] = Admin::where('id', $user->id)->get();


    // Render the edit view with the data
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.users.edit', $data)->render()]);
  }



  public function create(Program $program)
  {
    $data['program'] = $program;
    $data['accessList'] = new AdminAccessList();
    $data['users'] = Admin::whereDoesntHave('accessiblePrograms', function ($q) use ($program) {
      $q->where('accessable_id', $program->id);
    })->get();
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.users.edit', $data)->render()]);
  }

  public function update(Request $request, Program $program, Admin $user)
  {
    $att = $request->validate([
      'granted_till' => 'required|date',
    ]);

    AdminAccessList::ofProgram($program->id)
      ->where('admin_id', $user->id)
      ->update($att);

    return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => AdminAccessList::DT_ID, 'close' => 'globalModal']);
  }

  public function store(Program $program, Request $request)
  {
    $att = $request->validate([
      'user' => 'required|exists:admins,id',
      'granted_till' => 'required_if:permanent_access,false|date|nullable',
    ]);

    // Check if user is already added
    $isAlreadyAdded = Admin::where('id', $att['user'])->whereHas('accessiblePrograms', function ($q) use ($program) {
      $q->where('accessable_id', $program->id);
    })->exists();

    if ($isAlreadyAdded) {
      return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'User Already Added To This Program']);
    }

    $program->auditAttach('users', [$att['user']], ['granted_till' => $att['granted_till'], 'accessable_type' => Program::class]);

    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => AdminAccessList::DT_ID, 'close' => 'globalModal']);
  }

  public function destroy_OLD(Program $program, Admin $user)
  {
    if ($program->auditDetach('users', $user)) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => AdminAccessList::DT_ID]);
    }
  }

  public function destroy(Program $program, Admin $user)
  {
    // Find the related record in the ProgramUser table or similar
    $programUser = AdminAccessList::ofProgram($program->id)
      ->where('admin_id', $user->id)
      ->first();

    // If the record exists, perform a soft delete
    if ($programUser) {
      $programUser->delete();
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => AdminAccessList::DT_ID]);
    }

    // Handle the case where the record does not exist or deletion fails
    return response()->json(['message' => 'Deletion failed'], 422);
  }
}
