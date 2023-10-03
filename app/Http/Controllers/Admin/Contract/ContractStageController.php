<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\StagesDataTable;
use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Stage\StageStoreRequest;
use App\Models\Contract;
use App\Models\ContractStage;
use Illuminate\Http\Request;

class ContractStageController extends Controller
{
  public function index(Contract $contract, StagesDataTable $dataTable)
  {
    $dataTable->contract = $contract;

    $page = 'Project';
    if (request()->route()->getName() == 'admin.contracts.stages.index') {
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }

    return $dataTable->render('admin.pages.contracts.stages.index', compact('contract', 'page'));
    // return view('admin.pages.contracts.stages.index')
  }

  public function create(Contract $contract)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $stage = new ContractStage();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.stages.create', compact('project', 'contract', 'stage'))->render()]);
  }

  public function store(Contract $contract, StageStoreRequest $request)
  {
    $contract->load('project', 'stages');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $stage = $contract->stages()->create(
      [
        'stage_amount' => $request->stage_amount,
        'remaining_amount' => $request->stage_amount
      ]
        + $request->only(['name', 'description', 'status', 'start_date', 'due_date'])
    );

    $contract->update(['remaining_amount' => $contract->remaining_amount - $stage->stage_amount]);

    $message = auth()->user()->name . ' created a new stage: ' . $stage->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'stage-list', $message))->toOthers();

    return $this->sendRes(__('Stage Created Successfully'), ['event' => 'table_reload', 'table_id' => 'stages-table', 'close' => 'globalModal']);
  }

  public function edit(Contract $contract, ContractStage $stage)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $stage->project_id != $project->id, 403);

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.stages.create', compact('contract', 'project', 'stage'))->render()]);
  }

  public function update(Request $request, Contract $contract, ContractStage $stage)
  {
    $contract->load('project', 'stages');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $stage->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_stages,name,' . $stage->id . ',id,contract_id,' . $contract->id,
      'stage_amount' => ['required', 'numeric', 'gt:0', 'max:' . $contract->remaining_cost($stage->id)],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ], [
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $stageAmountDiff = $stage->stage_amount - $request->stage_amount;

    $stage->update(
      [
        'stage_amount' => $request->stage_amount,
        'remaining_amount' => $stage->remaining_amount - $stageAmountDiff
      ]
        + $request->only(['name', 'description', 'status', 'start_date', 'due_date'])
    );

    $message = auth()->user()->name . ' updated stage: ' . $stage->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'stage-list', $message))->toOthers();

    return $this->sendRes(__('Stage Updated Successfully'), ['event' => 'table_reload', 'table_id' => 'stages-table', 'close' => 'globalModal']);
  }

  public function destroy(Contract $contract, ContractStage $stage)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $stage->project_id != $project->id, 403);

    $stage->phases()->delete();

    $stage->delete();

    $message = auth()->user()->name . ' deleted stage: ' . $stage->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'stage-list', $message))->toOthers();

    return $this->sendRes(__('Stage Deleted Successfully'), ['event' => 'table_reload', 'table_id' => 'stages-table']);
  }
}
