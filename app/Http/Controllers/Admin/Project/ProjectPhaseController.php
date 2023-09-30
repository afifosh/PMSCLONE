<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Contract\PhasesDataTable;
use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Phase\PhaseStoreRequest;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use Illuminate\Http\Request;

class ProjectPhaseController extends Controller
{
  public function index($project, Contract $contract, string|ContractStage $stage, PhasesDataTable $dataTable)
  {
    $dataTable->stage = $stage;
    $dataTable->contract_id = $contract->id;
    $project = $contract->project ?? 'project';

    // abort_if(!$project->isMine(), 403);

    $page = 'Project';
    if (request()->route()->getName() == 'admin.contracts.stages.phases.index') {
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }
    return $dataTable->render('admin.pages.contracts.phases.index', compact('contract', 'project', 'stage', 'page'));

    return view('admin.pages.contracts.phases.index', compact('contract', 'project', 'phase_statuses', 'colors', 'page', 'stage'));
  }

  public function contractPhases(Contract $contract, $stage, PhasesDataTable $dataTable)
  {
    $stage = ContractStage::find($stage) ?? 'stage';

    return $this->index('project', $contract, $stage, $dataTable);
  }

  public function create($project, Contract $contract, $stage)
  {
    $stage = ContractStage::find($stage) ?? 'stage';
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // stage is instance of ContractStage then get stage remaining amount, else get contract remaining amount
    $remaining_amount = $stage instanceof ContractStage ? $stage->stage_amount : $contract->value;
    // abort_if(!$project->isMine(), 403);

    $phase = new ContractPhase();

    $title = $stage instanceof ContractStage ? 'Add Phase to ' . $stage->name : 'Add Phase to Contract As Commited Phase';

    return $this->sendRes('success', ['modalTitle' => $title, 'view_data' => view('admin.pages.contracts.phases.create', compact('project', 'contract', 'phase', 'stage', 'remaining_amount'))->render()]);
  }

  public function store($project, Contract $contract, $stage, PhaseStoreRequest $request)
  {
    $contract->load('project');
    $stage = ContractStage::find($stage) ?? 'stage';
    $project = $contract->project ?? 'project';
    // $stage->load('contract');
    // abort_if(!$project->isMine(), 403);

    $phase = $contract->phases()->create(
      [
        'estimated_cost' => $request->estimated_cost,
        'stage_id' => $stage->id ?? null
      ]
        + $request->only(['name', 'description', 'status', 'start_date', 'due_date'])
    );

    // If stage is null, then it is commited phase so update contract remaining amount
    if ($stage instanceof ContractStage)
      $stage->update(['remaining_amount' => $stage->remaining_amount - $phase->estimated_cost]);
    else
      $contract->update(['remaining_amount' => $contract->remaining_amount - $phase->estimated_cost]);

    $message = auth()->user()->name . ' created a new phase: ' . $phase->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function edit($project, Contract $contract, $stage, ContractPhase $phase)
  {
    $stage = ContractStage::find($stage) ?? 'stage';
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);
    $remaining_amount = $stage instanceof ContractStage ? $stage->stage_amount : $contract->value;

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'project', 'phase', 'stage', 'remaining_amount'))->render()]);
  }

  public function update($project, Request $request, Contract $contract, $stage, ContractPhase $phase)
  {
    $contract->load('project', 'phases');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $phase->id . ',id,stage_id,' . $phase->stage_id,
      'estimated_cost' => ['required', 'numeric', 'min:0'], //'max:'.$contract->remaining_cost($phase->id)
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $contract->end_date,
    ], [
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $costDiff = $phase->estimated_cost - $request->estimated_cost;

    $phase->update(['estimated_cost' => $contract->formatPhaseValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    if ($phase->stage_id) {
      if($phase->is_committed){
        $phase->stage->update(['allowable_amount' => $phase->stage->allowable_amount - $costDiff]);
        $phase->contract->update(['remaining_amount' => $phase->contract->remaining_amount - $costDiff]);
      }else{
        $phase->stage->update(['remaining_amount' => $phase->stage->remaining_amount + $costDiff]);
      }
    } else {
      $contract->update(['remaining_amount' => $contract->remaining_amount + $costDiff]);
    }

    // TODO : if stage is null, then it is commited phase so update contract remaining amount

    $message = auth()->user()->name . ' updated phase: ' . $phase->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function destroy($project, Contract $contract, $stage, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    $phase->load('stage', 'contract');
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    if ($phase->stage_id) {
      $data = [];
      if ($phase->is_committed) {
        $data['allowable_amount'] = $phase->stage->allowable_amount - $phase->estimated_cost;
      } else {
        $data['remaining_amount'] = $phase->stage->remaining_amount + $phase->estimated_cost;
      }
      $phase->stage->update($data);
    } else
      $contract->update(['remaining_amount' => $contract->remaining_amount + $phase->estimated_cost]);

    $phase->delete();

    $message = auth()->user()->name . ' deleted phase: ' . $phase->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Deleted Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function sortPhases($project, Contract $contract, $stage, Request $request)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $request->validate([
      'phases' => 'required|array',
      'phases.*' => 'required|integer|exists:contract_phases,id',
    ]);

    foreach ($request->phases as $order => $phase_id) {
      $contract->phases()->where('id', $phase_id)->update(['order' => $order]);
    }

    $message = auth()->user()->name . ' sorted phase list';

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phases Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }
}
