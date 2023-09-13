<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Contract\MilestonesDataTable;
use App\Events\Admin\ProjectMilestoneUpdated;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractMilestone;
use App\Models\ContractPhase;
use Illuminate\Http\Request;

class ProjectMilestoneController extends Controller
{
  public function index($project, Contract $contract, ContractPhase $phase, MilestonesDataTable $dataTable)
  {
    $dataTable->phase = $phase;
    $project = $contract->project ?? 'project';

    // abort_if(!$project->isMine(), 403);

    $page = 'Project';
    if(request()->route()->getName() == 'admin.contracts.phases.milestones.index'){
      $page = 'Contract';
      $contract->load('notifiableUsers');
    }
    return $dataTable->render('admin.pages.contracts.milestones.index', compact('contract', 'project', 'phase', 'page'));

    return view('admin.pages.contracts.milestones.index', compact('contract', 'project', 'milestone_statuses', 'colors', 'page', 'phase'));
  }

  public function contractMilestones(Contract $contract, ContractPhase $phase, MilestonesDataTable $dataTable){
    return $this->index('project', $contract, $phase, $dataTable);
  }

  public function create($project, Contract $contract, ContractPhase $phase)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $milestone = new ContractMilestone();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.milestones.create', compact('project', 'contract', 'milestone', 'phase'))->render()]);
  }

  public function store($project, Contract $contract, ContractPhase $phase, Request $request)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    $phase->load('contract');
    // abort_if(!$project->isMine(), 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_milestones,name,NULL,id,phase_id,' . $phase->id,
      'estimated_cost' => ['required', 'numeric', 'min:0'],//'max:'.$contract->remaining_cost()
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ],[
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $milestone = $phase->milestones()->create(['estimated_cost' => $contract->formatMilestoneValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' created a new milestone: ' . $milestone->name;

    if($contract->project)
    broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Created Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function edit($project,Contract $contract, ContractPhase $phase, ContractMilestone $milestone)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.milestones.create', compact('contract', 'project', 'milestone', 'phase'))->render()]);
  }

  public function update($project, Request $request, Contract $contract, ContractPhase $phase, ContractMilestone $milestone)
  {
    $contract->load('project', 'milestones');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:contract_milestones,name,' . $milestone->id . ',id,phase_id,' . $phase->id,
      'estimated_cost' => ['required', 'numeric', 'min:0'], //'max:'.$contract->remaining_cost($milestone->id)
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date|before_or_equal:due_date|after_or_equal:' . $contract->start_date,
      'due_date' => 'required|date|after:start_date|before_or_equal:' . $contract->end_date,
    ],[
      'due_date.before_or_equal' => 'The due date must be a date before or equal to contract end date.'
    ]);

    $milestone->update(['estimated_cost' => $contract->formatMilestoneValue($request->estimated_cost)] + $request->only(['name', 'description', 'status', 'start_date', 'due_date']));

    $message = auth()->user()->name . ' updated milestone: ' . $milestone->name;

    if($contract->project)
    broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Updated Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

  public function destroy($project, Contract $contract, ContractPhase $phase, ContractMilestone $milestone)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine() || $milestone->project_id != $project->id, 403);

    $milestone->delete();

    $message = auth()->user()->name . ' deleted milestone: ' . $milestone->name;

    if($contract->project)
    broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestone Deleted Successfully'), ['event' => 'table_reload', 'table_id' => 'milstones-table', 'close' => 'globalModal']);
  }

public function sortMilestones($project, Contract $contract, $phase, Request $request)
  {
    $contract->load('project');
    $project = $contract->project ?? 'project';
    // abort_if(!$project->isMine(), 403);

    $request->validate([
      'milestones' => 'required|array',
      'milestones.*' => 'required|integer|exists:contract_milestones,id',
    ]);

    foreach ($request->milestones as $order => $milestone_id) {
      $contract->milestones()->where('id', $milestone_id)->update(['order' => $order]);
    }

    $message = auth()->user()->name . ' sorted milestone list';

    if($contract->project)
    broadcast(new ProjectMilestoneUpdated($project, 'milestone-list', $message))->toOthers();

    return $this->sendRes(__('Milestones Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshMilestoneList']);
  }
}
