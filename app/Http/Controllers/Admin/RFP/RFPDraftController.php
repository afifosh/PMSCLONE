<?php

namespace App\Http\Controllers\Admin\RFP;

use App\DataTables\Admin\ProgramUsersDataTable;
use App\DataTables\Admin\RFP\DraftRfpsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\RFPDraft;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RFPDraftController extends Controller
{
  public function index(DraftRfpsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.rfp.index');
    // return view('admin.pages.partner.companies.index');
  }
  public function create($program_id = null)
  {
    $program_id ?
      $data['programs'] = Program::mine()->where('id',$program_id)->pluck('name', 'id')
      : $data['programs'] = Program::mine()->pluck('name', 'id')->prepend('Select Program', '');
    $data['draft_rfp'] = new RFPDraft();

    return $this->sendRes('success', ['view_data' => view('admin.pages.rfp.edit', $data)->render()]);
  }

  public function store(Request $request)
  {
    $att = $request->validate([
      'name' => 'required|string|max:255|unique:rfp_drafts,name',
      'program_id' => 'required|exists:programs,id',
      'description' => 'required|string',
    ]);
    RFPDraft::create($att + ['uuid' => Str::uuid()]);
    return $this->sendRes('Created Successfully', ['event' => 'table_reload', 'table_id' => RFPDraft::DT_ID, 'close' => 'globalModal']);
  }

  public function show(Request $request, $draft_rfp)
  {
    $draft_rfp = RFPDraft::where('id', $draft_rfp)->mine()->with('program')->firstOrFail();

    if($request->tab == 'program-users'){
      return $this->programUsers($request, $draft_rfp);
    }

    return view('admin.pages.rfp.show', compact('draft_rfp'));
  }

  public function draft_users_tab($draft_rfp, Request $request)
  {
    $draft_rfp = RFPDraft::where('id', $draft_rfp)->mine()->with('program')->firstOrFail();

    request()->program = $draft_rfp->program;
    $dataTable = new ProgramUsersDataTable();
    return $dataTable->render('admin.pages.rfp.show-users', compact('draft_rfp'));
  }

  public function draft_activity_tab($draft_rfp, Request $request)
  {
    $draft_rfp = RFPDraft::mine()->findOrFail($draft_rfp);

    $audits = $draft_rfp->audits()->with('user')->latest()->paginate();

    return view('admin.pages.rfp.draft-activity', compact('draft_rfp', 'audits'));

    // request()->program = $draft_rfp->program;
    // $dataTable = new ProgramUsersDataTable();
    // return $dataTable->render('admin.pages.rfp.show-users', compact('draft_rfp'));
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
}
