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
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.reminders-index', compact('task'))->render()]);
  }

  public function store($project, Task $task, Request $request)
  {
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
    $reminder->delete();
    return $this->sendRes('Reminder deleted successfully', []);
  }
}
