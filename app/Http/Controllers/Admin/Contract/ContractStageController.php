<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\StagesDataTable;
use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Stage\StageStoreRequest;
use App\Http\Requests\Admin\Contract\Stage\StageUpdateRequest;
use App\Models\Contract;
use App\Models\ContractStage;

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

    $stage = $contract->stages()->create($request->only(['name', 'description', 'status', 'start_date', 'due_date', 'stage_amount']));

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

  public function update(StageUpdateRequest $request, Contract $contract, ContractStage $stage)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    $stage->load('phases');

    $stage->update($request->only(['name', 'description', 'status', 'start_date', 'due_date', 'stage_amount']));

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
