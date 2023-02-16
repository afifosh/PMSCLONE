<?php

namespace App\Http\Controllers\Admin\RFP;

use App\DataTables\Admin\ProgramUsersDataTable;
use App\DataTables\Admin\RFP\DraftRfpsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\RFPDraft;
use Illuminate\Http\Request;

class RFPDraftController extends Controller
{
  public function index(DraftRfpsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.rfp.index');
    // return view('admin.pages.partner.companies.index');
  }
  public function create()
  {
    $data['draft_rfp'] = new RFPDraft();
    $data['programs'] = Program::mine()->pluck('name', 'id')->prepend('Select Program', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255|unique:rfp_drafts,name',
      'program_id' => 'required|exists:programs,id',
      'description' => 'required|string',
    ]);
    RFPDraft::create($att);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => RFPDraft::DT_ID, 'close' => 'globalModal']);
  }

  public function show(Request $request, $draft_rfp)
  {
    $draft_rfp = RFPDraft::where('id', $draft_rfp)->mine()->with('program')->firstOrFail();

    if($request->tab == 'program-users'){
      return $this->programUsers($request, $draft_rfp);
    }elseif($request->tab == 'files'){
      return $this->rfpFiles($request, $draft_rfp);
    }

    return view('admin.pages.rfp.show', compact('draft_rfp'));
  }

  public function edit(RFPDraft $draft_rfp)
  {
    $programs = Program::pluck('name', 'id')->prepend('Select Program', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.edit', compact('draft_rfp', 'programs'))->render()]);
  }

  public function update(Request $request, RFPDraft $draft_rfp)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255|unique:rfp_drafts,name,'.$draft_rfp->id,
      'program_id' => 'required|exists:programs,id',
      'description' => 'required|string',
    ]);
    if ($draft_rfp->update($att)) {
      return $this->sendRes('Updated Successfully', ['event' => 'table_reload', 'table_id' => RFPDraft::DT_ID, 'close' => 'globalModal']);
    }
  }

  public function destroy(RFPDraft $draft_rfp)
  {
    if ($draft_rfp->delete()) {
      return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => RFPDraft::DT_ID]);
    }
  }

  // Extra functions
  protected function programUsers(Request $request, $draft_rfp)
  {
    request()->program = Program::mine()->findOrFail(request('program'));
    $dataTable = new ProgramUsersDataTable();
    return $dataTable->render('admin.pages.rfp.show-users', compact('draft_rfp'));
    // return view('admin.pages.rfp.show-users');
  }

  protected function rfpFiles(Request $request, $draft_rfp)
  {
    $data['draft_rfp'] = $draft_rfp->load('files');
    return view('admin.pages.rfp.file-manager', compact('draft_rfp'));
  }
}
