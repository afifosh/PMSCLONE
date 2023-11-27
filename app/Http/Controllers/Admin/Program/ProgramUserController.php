<?php

namespace App\Http\Controllers\Admin\Program;

use App\DataTables\Admin\ProgramUsersDataTable;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Program;
use App\Models\ProgramUser;
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
      // Find the ProgramUser relationship
      $programUser = ProgramUser::where('program_id', $program->id)
                                ->where('admin_id', $user->id)
                                ->first();
  
      // Check if the ProgramUser relationship exists
      if (!$programUser) {
          // If not exist, handle accordingly, e.g., error message
          return $this->sendRes('error', ['message' => 'The specified relationship does not exist.']);
      }
  
      // Prepare data for the view
      $data['program'] = $program;
      $data['programUser'] = $programUser;
      $data['programUser'] = $programUser;

      // Instead of fetching and filtering all users, just get the specific user
      $data['users'] = Admin::where('id', $user->id)->get();
      
  
      // Render the edit view with the data
      return $this->sendRes('success', ['view_data' => view('admin.pages.programs.users.edit', $data)->render()]);
  }
  
  
  
  public function create(Program $program)
  {
    $data['program'] = $program;
    $data['programUser'] = new ProgramUser();
    $ids = ProgramUser::where('program_id', $program->id)->orWhere('program_id', $program->parent_id)->pluck('admin_id');
    $data['users'] = Admin::whereNotIn('id', $ids)->get();
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.users.edit', $data)->render()]);
  }

  public function update(Request $request, Program $program, Admin $user)
  {
      // Set default value for 'permanent_access' if it's not present in the request
      $request->merge(['permanent_access' => $request->boolean('permanent_access')]);

      // Validate the request data 
      $att = $request->validate([
          'permanent_access' => 'boolean',
          'until_at' => 'required_if:permanent_access,false|date|nullable',
      ]);

      // Find the existing ProgramUser relationship
      $programUser = ProgramUser::where('program_id', $program->id)
                                ->where('admin_id', $user->id) // Corrected here
                                ->firstOrFail();
                       
      // Update the ProgramUser record with the validated data
      $programUser->permanent_access = $att['permanent_access'];
      $programUser->until_at = $att['until_at'];
      $programUser->save();

      // Additional actions (like auditing) can be performed here
  
      // Return a response
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID, 'close' => 'globalModal']);
  }
  
public function store(Program $program, Request $request)
{

    // Set default value for 'permanent_access' if it's not present in the request
    // Set default value for 'permanent_access'
    $request->merge(['permanent_access' => $request->boolean('permanent_access')]);


    $att = $request->validate([
        'user' => 'required|exists:admins,id',
        'permanent_access' => 'boolean',
        'until_at' => 'required_if:permanent_access,false|date|nullable',
    ]);

    // Check if user is already added
    $isAlreadyAdded = ProgramUser::where(function ($q) use ($program) {
        $q->where('program_id', $program->id)->orWhere('program_id', $program->parent_id);
    })->where('admin_id', $att['user'])->exists();

    if ($isAlreadyAdded) {
        return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'User Already Added To This Program']);
    }

    $program->auditAttach('users', [$att['user']], ['added_by' => auth()->id(), 'permanent_access' => $att['permanent_access'], 'until_at' => $att['until_at']]);

    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID, 'close' => 'globalModal']);
}

  public function destroy_OLD(Program $program, Admin $user)
  {
    if ($program->auditDetach('users', $user)) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID]);
    }
  }

  public function destroy(Program $program, Admin $user)
  {
      // Find the related record in the ProgramUser table or similar
      $programUser = ProgramUser::where('program_id', $program->id)
                                ->where('admin_id', $user->id)
                                ->first();
  
      // If the record exists, perform a soft delete
      if ($programUser) {
          $programUser->delete();
          return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID]);
      }
  
      // Handle the case where the record does not exist or deletion fails
      return response()->json(['message' => 'Deletion failed'], 422);
  }
  
}
