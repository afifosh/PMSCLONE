<?php

namespace App\Http\Controllers\Admin\Project;

use App\Events\Admin\ProjectPhaseUpdated;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectPhase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Date\Carbon;

class ProjectPhaseController extends Controller
{
  public function index(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    $project->load('phases');

    $phase_statuses = ProjectPhase::getPossibleEnumValues('status');

    $colors = ['Not Started' => 'danger', 'In Progress' => 'warning', 'On Hold' => 'warning', 'Awaiting Feedback' => 'warning', 'Completed' => 'success'];

    if(request()->ajax()){
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.phases.phase-list', compact('project', 'phase_statuses', 'colors'))->render()]);
    }

    return view('admin.pages.projects.phases.index', compact('project', 'phase_statuses', 'colors'));
  }

  public function create(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    $phase_statuses = ProjectPhase::getPossibleEnumValues('status');

    $phase = new ProjectPhase();

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.phases.create', compact('project', 'phase', 'phase_statuses'))->render()]);
  }

  public function store(Project $project, Request $request)
  {
    abort_if(!$project->isMine(), 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:project_phases,name,NULL,id,project_id,' . $project->id,
      'estimated_cost' => 'required|max:255',
      'description' => 'required|string|max:65000',
      'status' => 'required|in:' . implode(',', ProjectPhase::getPossibleEnumValues('status')),
      'duration' => 'required',
    ]);

    try {
      [$startDate, $endDate] = explode(' to ', $request->duration);
      $startDate = Carbon::parse($startDate);
      $endDate = Carbon::parse($endDate);
    } catch (Exception $e) {
      throw ValidationException::withMessages(['duration' => 'Invalid duration']);
    }

    $phase = $project->phases()->create($request->only(['name', 'estimated_cost', 'description', 'status']) + [
      'start_date' => $startDate,
      'due_date' => $endDate,
    ]);

    $message = auth()->user()->name . ' created a new phase: ' . $phase->name;

    broadcast(new ProjectPhaseUpdated($phase, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Created Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList', 'close' => 'globalModal']);
  }

  public function edit(Project $project, ProjectPhase $phase)
  {
    abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $phase_statuses = ProjectPhase::getPossibleEnumValues('status');

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.phases.create', compact('project', 'phase', 'phase_statuses'))->render()]);
  }

  public function update(Project $project, Request $request, ProjectPhase $phase)
  {
    abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $request->validate([
      'name' => 'required|string|max:255|unique:project_phases,name,' . $phase->id . ',id,project_id,' . $project->id,
      'estimated_cost' => 'required|max:255',
      'description' => 'required|string|max:65000',
      'status' => 'required|in:' . implode(',', ProjectPhase::getPossibleEnumValues('status')),
      'duration' => 'required',
    ]);

    try {
      [$startDate, $endDate] = explode(' to ', $request->duration);
      $startDate = Carbon::parse($startDate);
      $endDate = Carbon::parse($endDate);
    } catch (Exception $e) {
      throw ValidationException::withMessages(['duration' => 'Invalid duration']);
    }

    $phase->update($request->only(['name', 'estimated_cost', 'description', 'status']) + [
      'start_date' => $startDate,
      'due_date' => $endDate,
    ]);

    $message = auth()->user()->name . ' updated phase: ' . $phase->name;

    broadcast(new ProjectPhaseUpdated($phase, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Updated Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList', 'close' => 'globalModal']);
  }

  public function destroy(Project $project, ProjectPhase $phase)
  {
    abort_if(!$project->isMine() || $phase->project_id != $project->id, 403);

    $phase->delete();

    $message = auth()->user()->name . ' deleted phase: ' . $phase->name;

    broadcast(new ProjectPhaseUpdated($phase, 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phase Deleted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }

  public function sortPhases(Project $project, Request $request)
  {
    abort_if(!$project->isMine(), 403);

    $request->validate([
      'phases' => 'required|array',
      'phases.*' => 'required|integer|exists:project_phases,id',
    ]);

    foreach ($request->phases as $order => $phase_id) {
      $project->phases()->where('id', $phase_id)->update(['order' => $order]);
    }

    $message = auth()->user()->name . ' sorted phase list';

    broadcast(new ProjectPhaseUpdated($project->phases()->first(), 'phase-list', $message))->toOthers();

    return $this->sendRes(__('Phases Sorted Successfully'), ['event' => 'functionCall', 'function' => 'refreshPhaseList']);
  }
}
