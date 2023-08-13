<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
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
}
