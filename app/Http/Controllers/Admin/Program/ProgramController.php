<?php

namespace App\Http\Controllers\Admin\Program;

use App\DataTables\Admin\ProgramsDataTable;
use App\DataTables\Admin\RFP\DraftRfpsDataTable;
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
    $data['programs'] = Program::where('parent_id', null)->pluck('name', 'id')->prepend('Select Program', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255|unique:programs,name',
      'program_code' => 'required|unique:programs,program_code|string|max:255',
      'parent_id' => 'nullable|exists:programs,id',
      'description' => 'nullable|string|max:2000',
    ]);
    Program::create($att);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID, 'close' => 'globalModal']);
  }

  public function show(Program $program)
  {
    $program = $program->where('id', $program->id)->mine()->firstOrFail();

    return view('admin.pages.programs.view', compact('program'));
  }

  public function edit(Program $program)
  {
    $data['program'] = $program;
    //$data['programs'] = Program::where([['id', '!=', $program->id], ['parent_id', null]])->pluck('name', 'id')->prepend(__('Select Program'), '');
    $data['programs'] = Program::where('id', '!=', $program->id)->pluck('name', 'id')->prepend(__('Select Program'), '');

    return $this->sendRes('success', ['view_data' => view('admin.pages.programs.edit', $data)->render()]);
  }

  public function update(Request $request, Program $program)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255|unique:programs,name,'.$program->id.',id',
      'program_code' => 'required|string|max:255|unique:programs,program_code,'.$program->id.',id',
      'parent_id' => 'nullable|exists:programs,id',
      'description' => 'nullable|string',
    ]);
    if ($program->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function destroy(Program $program)
  {
    if ($program->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => Program::DT_ID]);
    }
  }

  public function showDraftRFPs($program , DraftRfpsDataTable $dataTable)
  {
    $program = Program::mine()->findOrFail($program);
    $dataTable->setProgram($program->id);
    return $dataTable->render('admin.pages.rfp.index', compact('program'));
    return view('admin.pages.rfp.index');
  }
}
