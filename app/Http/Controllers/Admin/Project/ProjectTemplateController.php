<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\TemplatesDataTable;
use App\Http\Controllers\Controller;
use App\Models\CheckItemTemplate;
use App\Models\ProjectTemplate;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectTemplateController extends Controller
{
  public function index(TemplatesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.projects.templates.index');
    return view('admin.pages.projects.templates.index');
  }

  public function create()
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.create', ['template' => new ProjectTemplate])->render()]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255', Rule::unique('project_templates')->where('admin_id', auth()->id())],
      // 'tasks' => ['required', 'array'],
      // 'tasks.*' => ['required', 'exists:tasks,id'],
    ]);

    $template = ProjectTemplate::create([
      'name' => $request->name,
      'admin_id' => auth()->id(),
    ]);

    if($request->tasks[0] != null){
      $tasks = explode(',', array_unique($request->tasks)[0]);
      foreach ($tasks as $task) {
        $task = Task::find($task);
        $taskTemp = $template->taskTemplates()->create($task->toArray());
        $taskTemp->checkItemTemplates()->createMany($task->checklistItems->toArray());
      }
    }

    return $this->sendRes('Project template created successfully', ['event' => 'redirect', 'url' => route('admin.project-templates.index'), 'close' => 'globalModal']);
  }

  public function edit(ProjectTemplate $project_template)
  {
    $template = $project_template;
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.create', compact('template'))->render()]);
  }

  public function update(Request $request, ProjectTemplate $project_template)
  {
    $request->validate([
      'name' => ['required', 'string', 'max:255', Rule::unique('project_templates')->where('admin_id', auth()->id())->ignore($project_template->id)]
    ]);

    $project_template->update([
      'name' => $request->name,
    ]);

    return $this->sendRes('Project template updated successfully', ['event' => 'table_reload', 'table_id' => 'project-templates-datatable', 'close' => 'globalModal']);
  }

  public function destroy(ProjectTemplate $project_template)
  {
    $project_template->delete();

    return $this->sendRes('Project template deleted successfully', ['event' => 'table_reload', 'table_id' => 'project-templates-datatable']);
  }

  public function moveCheckItem(Request $request)
  {
    $request->validate([
      'from_id' => ['required', 'exists:task_templates,id'],
      'to_id' => ['required', 'exists:task_templates,id'],
      'check_item_id' => ['required', 'exists:check_item_templates,id'],
      'order' => ['required', 'array'],
      'order.*' => ['required', 'exists:check_item_templates,id'],
    ]);

    $checkItem = CheckItemTemplate::find($request->check_item_id);
    $checkItem->forceFill([
      'task_template_id' => $request->to_id,
    ])->save();

    $this->orderCheckItem($request);

    return $this->sendRes('success', []);
  }

  public function orderCheckItem(Request $request)
  {
    $request->validate([
      'order' => ['required', 'array'],
      'order.*' => ['required', 'exists:check_item_templates,id'],
    ]);

    foreach ($request->order as $key => $check_item_id) {
      CheckItemTemplate::where('id', $check_item_id)->update(['order' => $key]);
    }

    return $this->sendRes('success', []);
  }
}
