<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\PhasesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractPhaseController extends Controller
{
  public function index(Contract $contract, PhasesDataTable $dataTable)
  {
    $dataTable->contract = $contract;

    $page = 'Project';
    if (request()->route()->getName() == 'admin.contracts.phases.index') {
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }

    return $dataTable->render('admin.pages.contracts.phases.index', compact('contract', 'page'));
    // return view('admin.pages.contracts.phases.index')
  }

  public function contractMilestones(Contract $contract)
  {
    return $this->index('project', $contract);
  }

  public function create($project, Contract $contract)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $milestone = new ContractMilestone();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.milestones.create', compact('project', 'contract', 'milestone'))->render()]);
  }

  public function store($project, Contract $contract, Request $request)
  {
    $contract->load('project', 'milestones');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_milestones,name,NULL,id,contract_id,' . $contract->id,
      'estimated_cost' => ['required', 'numeric', 'min:0', 'max:' . $contract->remaining_cost()],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ], [
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $milestone = $contract->milestone()->create(['estimated_cost' => $contract->formatMilestoneValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' created a new milestone: ' . $milestone->name;

    if ($contract->project)
      broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Created Successfully'), ['event' => 'functionCall', 'function' => 'refreshMilestoneList', 'close' => 'globalModal']);
  }

  public function edit($project, Contract $contract, ContractMilestone $milestone)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.milestones.create', compact('contract', 'project', 'milestone'))->render()]);
  }

  public function update($project, Request $request, Contract $contract, ContractMilestone $milestone)
  {
    $contract->load('project', 'milestones');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_milestones,name,' . $milestone->id . ',id,contract_id,' . $contract->id,
      'estimated_cost' => ['required', 'numeric', 'min:0', 'max:' . $contract->remaining_cost($milestone->id)],
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ], [
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $milestone->update(['estimated_cost' => $contract->formatMilestoneValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' updated milestone: ' . $milestone->name;

    if ($contract->project)
      broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Updated Successfully'), ['event' => 'functionCall', 'function' => 'refreshMilestoneList', 'close' => 'globalModal']);
  }

  public function destroy($project, Contract $contract, ContractMilestone $milestone)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    $milestone->delete();

    $message = auth()->user()->name . ' deleted milestone: ' . $milestone->name;

    if ($contract->project)
      broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Deleted Successfully'), ['event' => 'functionCall', 'function' => 'refreshMilestoneList']);
  }
}
