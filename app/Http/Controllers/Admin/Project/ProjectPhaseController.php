<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Contract\PhasesDataTable;
use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Phase\PhaseStoreRequest;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\ContractStage;
use App\Models\Tax;
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
    $tax_rates = Tax::where('is_retention', false)->where('status', 'Active')->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('project', 'contract', 'phase', 'stage', 'remaining_amount', 'tax_rates'))->render()]);
  }

  public function store($project, Contract $contract, ContractStage $stage, PhaseStoreRequest $request)
  {
    $contract->load('project');
    // $stage = ContractStage::find($stage) ?? 'stage';
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $phase = $contract->phases()->create(
      [
        'estimated_cost' => $request->estimated_cost,
        'stage_id' => $stage->id ?? null
      ]
        + $request->only(['name', 'description', 'status', 'start_date', 'due_date'])
    );

    $this->storeTaxes($phase, $request->phase_taxes);

    $phase->updateTaxAmount();

    $stage->update(['remaining_amount' => $stage->remaining_amount - $phase->estimated_cost]);

    $message = auth()->user()->name . ' created a new phase: ' . $phase->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  protected function storeTaxes($phase, $taxes): void
  {
    $taxes = Tax::whereIn('id', filterInputIds($taxes))->where('is_retention', false)->where('status', 'Active')->get();

    $sync_data = [];
    foreach ($taxes as $rate) {
      $sync_data[$rate->id] = ['amount' => $rate->amount * 1000, 'type' => $rate->type, 'contract_phase_id' => $phase->id];
    }

    $phase->taxes()->sync($sync_data);
  }

  public function edit($project, Contract $contract, ContractStage $stage, ContractPhase $phase)
  {
    // $stage = ContractStage::find($stage) ?? 'stage';
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);
    $remaining_amount = $stage instanceof ContractStage ? $stage->stage_amount : $contract->value;

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'project', 'phase', 'stage', 'remaining_amount'))->render()]);
  }

  public function update($project, Request $request, Contract $contract, $stage, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    $phase->load('stage');
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $phase->id . ',id,stage_id,' . $phase->stage_id,
      'estimated_cost' => ['required', 'numeric', 'gt:0', 'max:' . $phase->stage->remaining_amount + $phase->estimated_cost],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date'. (request()->due_date ? '|before_or_equal:due_date' : '' ).'|after_or_equal:' . $phase->stage->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $phase->stage->due_date,
    ], [
      'due_date.before_or_equal' => 'The due date must be a date before or equal to state due date.'
    ]);

    $costDiff = $phase->estimated_cost - $request->estimated_cost;

    $phase->update($request->only(['name', 'description', 'status', 'start_date', 'due_date', 'estimated_cost']));
    $phase->stage->update(['remaining_amount' => $phase->stage->remaining_amount + $costDiff]);

    $message = auth()->user()->name . ' updated phase: ' . $phase->name;

    if ($contract->project)
      broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function destroy($project, Contract $contract, $stage, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    $phase->load('stage');
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $phase->stage->update(['remaining_amount' => $phase->stage->remaining_amount + $phase->estimated_cost]);

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
