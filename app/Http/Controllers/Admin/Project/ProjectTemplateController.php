<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\TemplatesDataTable;
use App\Http\Controllers\Controller;
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
      'tasks' => ['required', 'array'],
      'tasks.*' => ['required', 'exists:tasks,id'],
    ]);

    $template = ProjectTemplate::create([
      'name' => $request->name,
      'admin_id' => auth()->id(),
    ]);

    $tasks = explode(',', array_unique($request->tasks)[0]);

    foreach ($tasks as $task) {
      $task = Task::find($task);
      $taskTemp = $template->taskTemplates()->create($task->toArray());
      $taskTemp->checkItemTemplates()->createMany($task->checklistItems->toArray());
    }

    return $this->sendRes('Project template created successfully', ['event' => 'redirect', 'url' => route('admin.project-templates.index')]);
  }
}
