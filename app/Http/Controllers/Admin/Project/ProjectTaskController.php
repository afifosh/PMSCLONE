<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectTasksDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectTaskStoreRequest;
use App\Models\Admin;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Project $project, ProjectTasksDataTable $dataTable)
  {
    abort_if(!$project->isMine(), 403);

    $dataTable->project_id = $project->id;
    $summary = Task::selectRaw('status, COUNT(*) as task_count')
      ->where('project_id', $project->id)
      ->groupBy('status')
      ->get();

    return $dataTable->render('admin.pages.projects.tasks.index', ['project' => $project, 'summary' => $summary]);
    // view('admin.pages.projects.tasks.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    $data['task'] = new Task();
    $data['project'] = $project;
    $data['admins'] = Admin::get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Project $project, ProjectTaskStoreRequest $request)
  {
    abort_if(!$project->isMine(), 403);

    $task = $project->tasks()->create($request->validated() + ['admin_id' => auth()->id()]);
    $task->assignees()->sync($request->assignees);
    $task->followers()->sync($request->followers);

    return $this->sendRes('Task created successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Project $project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->load('project', 'checklistItems');

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.show', compact('task'))->render(), 'JsMethods' => ['initSortable', 'initDropZone', 'initEditor']]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Project $project,Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $data['task'] = $task;
    $data['project'] = $project;
    $data['admins'] = Admin::get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.create', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update($project, ProjectTaskStoreRequest $request, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->update($request->validated());
    $task->assignees()->sync($request->assignees);
    $task->followers()->sync($request->followers);

    return $this->sendRes('Task Updated successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable', 'close' => 'globalModal']);
  }

  public function hideCompleted($project, Task $task, $status = false)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->update(['is_completed_checklist_hidden' => $status ? 1 : 0]);

    return $this->sendRes('Task Updated successfully', ['JsMethods' => ['reload_task_checklist']]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->delete();

    return $this->sendRes('Task deleted successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable']);
  }
}
