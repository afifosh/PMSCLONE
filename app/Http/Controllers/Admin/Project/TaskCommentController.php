<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Request;

class TaskCommentController extends Controller
{
  public function index($project, Task $task)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.comments-index', compact('task'))->render()]);
  }

  public function store($project, Task $task, Request $request)
  {
    $request->validate([
      'comment' => 'required|string'
    ]);

    $task->comments()->create($request->only(['comment']) + ['admin_id' => auth()->id()]);

    return $this->sendRes('Comment created successfully');
  }

  public function destroy($project, $task, TaskComment $comment)
  {
    $comment->delete();
    return $this->sendRes('Comment deleted successfully');
  }
}
