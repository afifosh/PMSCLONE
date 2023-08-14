<?php

namespace App\Http\Controllers\Admin\Project;

use App\Events\Admin\ProjectTaskUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskCheckListItem;
use Illuminate\Http\Request;

class TaskBoardController extends Controller
{
  public function index(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    $project->load('tasks.checklistItems');
    $priorities = Task::getPossibleEnumValues('priority');
    $task_statuses = Task::getPossibleEnumValues('status');
    $colors = ['Low' => 'success', 'Medium' => 'warning', 'High' => 'danger', 'Urgent' => 'danger'];

    $myTasksCount = Task::where('project_id', $project->id)->whereHas('assignees', function($q){
      return $q->where('admin_id', auth()->id());
    })->count();

    if(request()->ajax()){
      $data['myTasksCount'] = $myTasksCount;
      foreach($priorities as $priority){
        $data['priority'][$priority] = $project->tasks->where('priority', $priority)->count();
      }
      foreach($task_statuses as $status){
        $data['status'][slug($status)] = $project->tasks->where('status', $status)->count();
      }
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.task-board.tasks-list', compact('project', 'priorities', 'task_statuses', 'colors'))->render(), 'data' => $data]);
    }

    return view('admin.pages.projects.task-board.index', compact('project', 'priorities', 'task_statuses', 'colors', 'myTasksCount'));
  }

  public function sortBoardTasks(Request $request, Project $project)
  {
    abort_if(!$project->isMine(), 403);
    request()->validate([
      'tasks' => 'required|array',
      'tasks.*.id' => ['required', 'exists:tasks,id'],
      'tasks.*.subtasks' => 'sometimes|array',
      'tasks.*.subtasks.*.id' => ['required', 'exists:task_check_list_items,id']
    ]);

    foreach($request->tasks as $i => $task){
      Task::where('project_id', $project->id)->where('id', $task['id'])->update(['order' => $i+1]);
      if(isset($task['subtasks'])){
        foreach($task['subtasks'] as $i => $checkItem){
          TaskCheckListItem::where('id', $checkItem['id'])->update(['task_id' => $task['id'], 'order' => $i+1]);
        }
      }
    }

    $chatMessage = auth()->user()->name . ' sorted tasks in project : "' . $project->name . '"';
    $task = Task::where('project_id', $project->id)->first();
    broadcast(new ProjectTaskUpdatedEvent($task, 'summary', $chatMessage))->toOthers();

    return $this->sendRes('success', ['message' => 'Tasks sorted successfully.']);
  }
}
