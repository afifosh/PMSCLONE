<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\CheckItemTemplate;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateTaskCheckItemController extends Controller
{
  public function index($project_template, TaskTemplate $task)
  {
    $task = $task->load('checkItemTemplates');

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.tasks.check-items.index', compact('task'))->render()]);
  }

  public function create($project_template, $task)
  {
    $task = TaskTemplate::find($task);
    $checkItem = new CheckItemTemplate();

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.tasks.check-items.create', compact('task', 'checkItem'))->render()]);
  }

  public function store(Request $request, $project_template, $task)
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255', Rule::unique('check_item_templates')->where('task_template_id', $task)],
    ]);

    $task = TaskTemplate::find($task);
    $task->checkItemTemplates()->create($request->only('title'));

    return $this->sendRes('Check item created successfully', ['event' => 'functionCall', 'function' => 'reloadCheckItems', 'function_params' => $task->id, 'close' => 'globalModal']);
  }

  public function edit($project_template, $task, CheckItemTemplate $checkItem)
  {
    $task = TaskTemplate::find($task);

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.tasks.check-items.create', compact('task', 'checkItem'))->render()]);
  }

  public function update(Request $request, $project_template, TaskTemplate $task, CheckItemTemplate $checkItem)
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255', Rule::unique('check_item_templates')->where('task_template_id', $checkItem->task_template_id)->ignore($checkItem->id)],
    ]);

    $checkItem->update($request->only('title'));

    return $this->sendRes('Check item updated successfully', ['event' => 'functionCall', 'function' => 'reloadCheckItems', 'function_params' => $task->id, 'close' => 'globalModal']);
  }

  public function destroy($project_template, TaskTemplate $task, CheckItemTemplate $checkItem)
  {
    $checkItem->delete();

    return $this->sendRes('Check item deleted successfully', ['event' => 'functionCall', 'function' => 'reloadCheckItems', 'function_params' => $task->id, 'close' => 'globalModal']);
  }
}
