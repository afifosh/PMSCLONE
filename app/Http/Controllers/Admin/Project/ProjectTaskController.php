<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectTasksDataTable;
use App\Events\Admin\ProjectTaskUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectTaskStoreRequest;
use App\Models\Admin;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use \Spatie\Comments\Enums\NotificationSubscriptionType;

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
    $data['admins'] = $project->members;

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.create', $data)->render()]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Project $project, ProjectTaskStoreRequest $request)
  {
    abort_if(!$project->isMine(), 403);

    $task = $project->tasks()->create($request->validated() + ['admin_id' => auth()->id()]);
    $task->assignees()->sync(remove_null_values($request->assignees));
    $task->followers()->sync(remove_null_values($request->followers));

    // subscribe notifications for both assignees and followers
    if ($task->assignees->count() > 0) {
      foreach ($task->assignees as $user) {
        $user->subscribeToCommentNotifications($task, NotificationSubscriptionType::All);
      }
    }

    if ($task->followers->count() > 0) {
      foreach ($task->followers as $user) {
        $user->subscribeToCommentNotifications($task, NotificationSubscriptionType::All);
      }
    }

    $chatMessage = auth()->user()->name. ' created a new task: '.$task->subject;
    broadcast(new ProjectTaskUpdatedEvent($task, 'new_task_added', $chatMessage))->toOthers();

    return $this->sendRes('Task created successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable', 'close' => 'globalModal']);
  }

  /**
   * Display the specified resource.
   */
  public function show(Project $project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);
    $logs = [];

    if ((!request()->tab && !request()->type) || request()->tab == 'summary')
      $task->load('project');
    else if (request()->tab == 'checklist')
      $task->load('checklist');
    else if (request()->tab == 'comments' || request()->type == 'comments-list')
      $task->load('comments');
    else if (request()->tab == 'files')
      $task->load('media');
    else if (request()->tab == 'logs' || request()->type == 'activities-list')
      $logs = $task->getPaginatedLogs();

    if (request()->type == 'summary-list')
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.show-summary', compact('task', 'logs'))->render()]);
    else if (request()->type == 'activities-list')
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.show-activities', compact('task', 'logs'))->render()]);
    else if (request()->type == 'comments-list')
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.show-comments', compact('task'))->render()]);

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.show', compact('task', 'logs'))->render(), 'JsMethods' => ['initSortable', 'initDropZone', 'initEditor']]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $data['task'] = $task;
    $data['project'] = $task->project;
    $data['admins'] = $task->project->members;

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.create', $data)->render()]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update($project, ProjectTaskStoreRequest $request, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->update($request->validated());
    $task->assignees()->sync(remove_null_values($request->assignees));
    $task->followers()->sync(remove_null_values($request->followers));

    $chatMessage = auth()->user()->name. ' updated task: '.$task->subject;
    broadcast(new ProjectTaskUpdatedEvent($task, 'summary', $chatMessage))->toOthers();

    return $this->sendRes('Task Updated successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable', 'close' => 'globalModal']);
  }

  public function hideCompleted($project, Task $task, $status = false)
  {
    abort_if(!$task->project->isMine(), 403);

    $task->update(['is_completed_checklist_hidden' => $status ? 1 : 0]);

    broadcast(new ProjectTaskUpdatedEvent($task, 'checklist'))->toOthers();

    return $this->sendRes('Task Updated successfully', ['JsMethods' => ['reload_task_checklist']]);
  }

  public function updateOrder(Project $project, Request $request)
  {
    abort_if(!$project->isMine(), 403);

      $request->validate([
        'order' => 'required|array',
        'order.*' => 'required|integer|exists:tasks,id',
      ]);

      foreach ($request->order as $key => $value) {
        $project->tasks()->where('id', $value)->update(['order' => $key + 1]);
      }

      $message = auth()->user()->name. ' updated task order';
      broadcast(new ProjectTaskUpdatedEvent($project->tasks()->first(), 'summary', $message))->toOthers();

      return true;
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy($project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    $message = auth()->user()->name. ' deleted task: '.$task->subject;

    $task->delete();

    broadcast(new ProjectTaskUpdatedEvent($task, 'task_deleted', $message))->toOthers();

    return $this->sendRes('Task deleted successfully', ['event' => 'table_reload', 'table_id' => 'project-tasks-datatable']);
  }
}
