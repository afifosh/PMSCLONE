<?php

namespace App\Http\Controllers\Admin\Project;

use App\Events\Admin\ProjectTaskUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskCheckListItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskChecklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($project, Task $task)
    {
      abort_if(!$task->project->isMine(), 403);

      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.checklist-index', compact('task'))->render()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($project, Task $task)
    {
      abort_if(!$task->project->isMine(), 403);

      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.checklist-form', compact('task'))->render()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($project, Task $task, Request $request)
    {
      abort_if(!$task->project->isMine(), 403);

      $request->validate([
        'title' => [
          'required',
          'string',
          'max:255',
          Rule::unique('task_check_list_items')->where(function ($query) use ($task) {
            return $query->where('task_id', $task->id)->where('title', request()->title)->whereNull('deleted_at');
          })
        ],
        'assigned_to' => 'nullable|exists:admins,id',
        'due_date' => 'nullable|date',
      ], [
        'title.unique' => 'Checklist item already exists',
      ]);

      $checklist = $task->checklistItems()->create($request->only(['title', 'assigned_to', 'due_date'])+ ['order' => $task->checklistItems()->count() + 1, 'created_by' => auth()->id()]);

      $message = auth()->user()->name . ' added a checklist item : "' . $checklist->title . '" in task : "' . $task->subject . '"';
      broadcast(new ProjectTaskUpdatedEvent($task, 'checklist', $message))->toOthers();

      if(request()->from == 'task-board'){
        return $this->sendRes('Checklist item created successfully', ['event' => 'functionCall', 'function' => 'refreshTaskList', 'close' => 'globalModal']);
      }

      return $this->sendRes('Checklist item created successfully', ['id' => $checklist->id, 'JsMethods' => ['reload_task_checklist', 'reset_checklist_form']]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskCheckListItem $checklistItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($project, Task $task, TaskCheckListItem $checklistItem)
    {
      abort_if(!$checklistItem->task->project->isMine(), 403);

      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.checklist-edit', compact('task', 'checklistItem'))->render()]);
    }

    public function update($project, $task, Request $request, TaskCheckListItem $checklistItem)
    {
      $request->validate([
        'title' => [
          'required',
          'string',
          'max:255',
          Rule::unique('task_check_list_items')->where(function ($query) use ($checklistItem) {
            return $query->where('task_id', $checklistItem->task_id)->where('title', request()->title)->whereNull('deleted_at')->whereNotIn('id', [$checklistItem->id]);
          })
        ],
        'assigned_to' => 'nullable|exists:admins,id',
        'due_date' => 'nullable|date',
      ], [
        'title.unique' => 'Checklist item already exists',
      ]);

      $checklistItem->update($request->only(['title', 'assigned_to', 'due_date']));

      if(request()->from == 'task-board'){
        return $this->sendRes('Checklist item updated successfully', ['event' => 'functionCall', 'function' => 'refreshTaskList', 'close' => 'globalModal']);
      }

      return $this->sendRes('Checklist item updated successfully', ['event' => 'redirect', 'url' => route('admin.projects.tasks.index', $project)]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_status($project, $task, Request $request, TaskCheckListItem $checklistItem)
    {
      abort_if(!$checklistItem->task->project->isMine(), 403);

      $request->validate([
        'status' => 'required',
      ]);

      if($checklistItem->status != $request->boolean('status'))
      $checklistItem->update(['status' => $request->boolean('status'), 'completed_by' => $request->boolean('status') ? auth()->id() : null]);

      $message = auth()->user()->name . ' marked checklist item : "' . $checklistItem->title . '" as ' . ($request->boolean('status') ? 'completed' : 'incomplete') . ' in task : "' . $checklistItem->task->subject . '"';

      broadcast(new ProjectTaskUpdatedEvent($checklistItem->task, 'checklist', $message))->toOthers();

      return $this->sendRes('Checklist item updated successfully');
    }

    public function updateOrder($project, Task $task, Request $request)
    {
      abort_if(!$task->project->isMine(), 403);

      $request->validate([
        'order' => 'required|array',
        'order.*' => 'required|integer|exists:task_check_list_items,id',
      ]);

      foreach ($request->order as $key => $value) {
        TaskCheckListItem::where('id', $value)->where('task_id', $task->id)->update(['order' => $key + 1]);
      }

      $message = auth()->user()->name . ' updated checklist item order in task : "' . $task->subject . '"';
      broadcast(new ProjectTaskUpdatedEvent($task, 'checklist', $message))->toOthers();

      return true;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project, $task, TaskCheckListItem $checklistItem)
    {
      abort_if(!$checklistItem->task->project->isMine(), 403);

      $id = $checklistItem->id;
      $message = auth()->user()->name . ' deleted checklist item : "' . $checklistItem->title . '" from task : "' . $checklistItem->task->subject . '"';
      $checklistItem->delete();

      broadcast(new ProjectTaskUpdatedEvent($checklistItem->task, 'checklist', $message))->toOthers();

      if(request()->from == 'task-board'){
        return $this->sendRes('Checklist item deleted successfully', ['event' => 'functionCall', 'function' => 'refreshTaskList', 'close' => 'globalModal']);
      }

      return $this->sendRes('Checklist item deleted successfully', ['disable_alert' => true, 'event' => 'functionCall', 'function' => 'handle_deleted_checklist', 'function_params' => route('admin.projects.tasks.checklist-items.restore', [$project, $task, $id])]);
    }

    public function restore($project, $task, $id)
    {
      $checklistItem = TaskCheckListItem::withTrashed()->findOrFail($id);

      abort_if(!$checklistItem->task->project->isMine(), 403);

      $checklistItem->restore();

      $message = auth()->user()->name . ' restored checklist item : "' . $checklistItem->title . '" in task : "' . $checklistItem->task->subject . '"';
      broadcast(new ProjectTaskUpdatedEvent($checklistItem->task, 'checklist', $message))->toOthers();

      return $this->sendRes('Checklist item restored successfully', ['event' => 'functionCall', 'function' => 'reload_task_checklist']);
    }
}
