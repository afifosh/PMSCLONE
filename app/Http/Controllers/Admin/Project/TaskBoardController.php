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
    $project->load('tasks.checklistItems');
    $priorities = Task::getPossibleEnumValues('priority');
    $task_statuses = Task::getPossibleEnumValues('status');

    return view('admin.pages.projects.task-board.index', compact('project', 'priorities', 'task_statuses'));
  }
}
