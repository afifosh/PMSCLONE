<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskReminder;
use Illuminate\Http\Request;

class TaskReminderController extends Controller
{
  public function index($project, Task $task)
  {
    abort_if(!$task->project->isMine(), 403);

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.reminders-index', compact('task'))->render()]);
  }

  public function store($project, Task $task, Request $request)
  {
    abort_if(!$task->project->isMine(), 403);

    $request->validate([
      'recipient_id' => 'required|exists:admins,id',
      'description' => 'required',
      'remind_at' => 'required|date',
      'can_send_email' => 'sometimes',
    ]);

    $task->reminders()->create($request->only(['recipient_id', 'description', 'remind_at']) + [
      'sender_id' => auth()->id(),
      'can_send_email' => $request->boolean('can_send_email'),
    ]);

    return $this->sendRes('Reminder added successfully.',['JsMethods' => ['update_reminder_form', 'reload_reminder_list']]);
  }

  public function destroy($project, $task, TaskReminder $reminder)
  {
    abort_if(!$reminder->task->project->isMine(), 403);

    $reminder->delete();

    return $this->sendRes('Reminder deleted successfully', []);
  }
}
