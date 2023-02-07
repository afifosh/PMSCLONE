<?php

namespace App\Http\Controllers\Admin\Program;

use App\DataTables\Admin\ProgramsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
  public function index(ProgramsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.programs.index');
    // return view('admin.pages.programs.index');
  }
  public function create()
  {
    $data['program'] = new Program();
    $data['programs'] = Program::pluck('name', 'id')->prepend('Select Program', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    // dd($request->all());
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'program_code' => 'required|unique:programs,program_code|string|max:255',
      'parent_id' => 'nullable|exists:programs,id',
      'description' => 'required',
    ]);
    Program::create($att);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID, 'close' => 'globalOffCanvas']);
  }

  public function show(Program $program)
  {
    return view('admin.pages.programs.view', compact('program'));
  }

  public function edit(Program $program)
  {
    $data['program'] = $program;
    $data['programs'] = Program::where('id', '!=', $program->id)->pluck('name', 'id');
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.edit', $data)->render()]);
  }

  public function update(Request $request, Program $program)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255',
      'program_code' => 'required|unique:programs,program_code,'.$program->id.',id|string|max:255',
      'parent_id' => 'sometimes|exists:programs,id',
      'description' => 'required',
    ]);
    if ($program->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID, 'close' => 'globalOffCanvas']);
    }
  }

  public function destroy(Program $program)
  {
    if ($program->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID]);
    }
  }
}
