<?php

namespace App\Http\Controllers\Admin\Project;

use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractPhase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\Core\Date\Carbon;

class ProjectPhaseController extends Controller
{
  public function index($project, Contract $contract)
  {
    $contract->load('phases', 'project');
    $project = $contract->project ?? 'project';

    // abort_if(!$project->isMine(), 403);

    $phase_statuses = ContractPhase::STATUSES;
    $colors = ContractPhase::STATUSCOLORS;

    if(request()->ajax()){
      return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.phase-list', compact('contract', 'project', 'phase_statuses', 'colors'))->render()]);
    }
    $page = 'Project';
    if(request()->route()->getName() == 'admin.contracts.phases.index'){
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }

    return view('admin.pages.contracts.phases.index', compact('contract', 'project', 'phase_statuses', 'colors', 'page'));
  }

  public function contractPhases(Contract $contract){
    return $this->index('project', $contract);
  }

  public function create($project, Contract $contract)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $phase = new ContractPhase();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('project', 'contract', 'phase'))->render()]);
  }

  public function store($project, Contract $contract,Request $request)
  {
    $contract->load('project', 'phases');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_phases,name,NULL,id,contract_id,' . $contract->id,
      'estimated_cost' => ['required', 'numeric', 'min:0','max:'.$contract->remaining_cost()],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ],[
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $phase = $contract->phases()->create(['estimated_cost' => $contract->formatPhaseValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' created a new phase: ' . $phase->name;

    if($contract->project)
    broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList', 'close' => 'globalModal']);
  }

  public function edit($project,Contract $contract, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.create', compact('contract', 'project', 'phase'))->render()]);
  }

  public function update($project, Request $request, Contract $contract, ContractPhase $phase)
  {
    $contract->load('project', 'phases');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_phases,name,' . $phase->id . ',id,contract_id,' . $contract->id,
      'estimated_cost' => ['required', 'numeric', 'min:0','max:'.$contract->remaining_cost($phase->id)],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ],[
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $phase->update(['estimated_cost' => $contract->formatPhaseValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' updated phase: ' . $phase->name;

    if($contract->project)
    broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList', 'close' => 'globalModal']);
  }

  public function destroy($project, Contract $contract, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $phase->delete();

    $message = auth()->user()->name . ' deleted phase: ' . $phase->name;

    if($contract->project)
    broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Deleted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }

public function sortPhases($project, Contract $contract, Request $request)
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

    if($contract->project)
    broadcast(new ProjectPhaseUpdated($project, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phases Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }
}