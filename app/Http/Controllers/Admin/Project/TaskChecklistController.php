<?php

namespace App\Http\Controllers\Admin\Project;

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
      return $this->sendRes('success', ['view_data' => view('admin.pages.projects.tasks.checklist-index', compact('task'))->render()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($project, Task $task, Request $request)
    {
      $request->validate([
        'title' => [
          'required',
          'string',
          'max:255',
          Rule::unique('task_check_list_items')->where(function ($query) use ($task) {
            return $query->where('task_id', $task->id)->where('title', request()->title);
          })
        ],
      ], [
        'title.unique' => 'Checklist item already exists',
      ]);

      $checklist = $task->checklistItems()->create($request->only(['title'])+ ['order' => $task->checklistItems()->count() + 1, 'created_by' => auth()->id()]);

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
    public function edit(TaskCheckListItem $checklistItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($project, $task, Request $request, TaskCheckListItem $checklistItem)
    {
      $request->validate([
        'status' => 'required',
      ]);

      if($checklistItem->status != $request->boolean('status'))
      $checklistItem->update(['status' => $request->boolean('status'), 'completed_by' => $request->boolean('status') ? auth()->id() : null]);

      return $this->sendRes('Checklist item updated successfully');
    }

    public function updateOrder($project, Task $task, Request $request)
    {
      $request->validate([
        'order' => 'required|array',
        'order.*' => 'required|integer|exists:task_check_list_items,id',
      ]);

      foreach ($request->order as $key => $value) {
        TaskCheckListItem::where('id', $value)->where('task_id', $task->id)->update(['order' => $key + 1]);
      }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($project, $task, TaskCheckListItem $checklistItem)
    {
      $checklistItem->delete();
      return $this->sendRes('Checklist item deleted successfully');
    }
}
