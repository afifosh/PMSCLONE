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
  public function create(Program $program)
  {
    $data['program'] = $program;
    $data['programUser'] = new ProgramUser();
    $ids = ProgramUser::where('program_id', $program->id)->orWhere('program_id', $program->parent_id)->pluck('admin_id');
    $data['users'] = Admin::whereNotIn('id', $ids)->get();
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.users.edit', $data)->render()]);
  }

  public function store(Program $program, Request $request)
  {
    $att = $request->validate([
      'users' => 'required|array',
      'users.*' => 'exists:admins,id',
    ]);

    $isAlreadyAdded = ProgramUser::where(function ($q) use ($program) {
      $q->where('program_id', $program->id)->orWhere('program_id', $program->parent_id);
    })->whereIn('admin_id', $att['users'])->exists();
    if ($isAlreadyAdded) {
      return $this->sendRes('', ['event' => 'functionCall', 'function' => 'toast_danger', 'function_params' => 'User Already Added To This Program']);
    }
    $program->users()->attach(array_unique($request->users), ['added_by' => auth()->id()]);

    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID, 'close' => 'globalModal']);
  }

  public function destroy(Program $program, Admin $user)
  {
    if ($program->users()->detach($user)) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => ProgramUser::DT_ID]);
    }
  }
}
