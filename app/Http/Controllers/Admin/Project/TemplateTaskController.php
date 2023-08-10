<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\ProjectTemplate;
use App\Models\TaskTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TemplateTaskController extends Controller
{
  public function index(ProjectTemplate $project_template)
  {
    $project_template->load('taskTemplates.checkItemTemplates');

    return view('admin.pages.projects.templates.tasks.index', compact('project_template'));
  }

  public function create(ProjectTemplate $project_template)
  {
    $task = new TaskTemplate();

    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.tasks.create', compact('project_template', 'task'))->render()]);
  }

  public function store(Request $request, ProjectTemplate $project_template)
  {
    $request->validate([
      'subject' => ['required', 'string', 'max:255', Rule::unique('task_templates')->where('project_template_id', $project_template)],
      'priority' => 'required',
      'tags' => 'nullable|array',
      'tags.*' => 'nullable|string|max:255',
      'description' => 'nullable|string|max:255',
    ]);

    $project_template->taskTemplates()->create($request->only('subject', 'priority', 'description', 'tags'));

    return $this->sendRes('Task created successfully', ['event' => 'redirect', 'url' => route('admin.project-templates.tasks.index', $project_template)]);
  }

  public function edit(ProjectTemplate $project_template, TaskTemplate $task)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.tasks.create', compact('project_template', 'task'))->render()]);
  }

  public function update(Request $request, ProjectTemplate $project_template, TaskTemplate $task)
  {
    $request->validate([
      'subject' => ['required', 'string', 'max:255', Rule::unique('task_templates')->where('project_template_id', $project_template)->ignore($task->id)],
      'priority' => 'required',
      'tags' => 'nullable|array',
      'tags.*' => 'nullable|string|max:255',
      'description' => 'nullable|string|max:255',
    ]);

    $task->update($request->only('subject', 'priority', 'description', 'tags'));

    return $this->sendRes('Task updated successfully', ['event' => 'redirect', 'url' => route('admin.project-templates.tasks.index', $project_template)]);
  }

  public function destroy(ProjectTemplate $project_template, TaskTemplate $task)
  {
    $task->delete();

    return $this->sendRes('Task deleted successfully', ['event' => 'redirect', 'url' => route('admin.project-templates.tasks.index', $project_template)]);
  }
}
