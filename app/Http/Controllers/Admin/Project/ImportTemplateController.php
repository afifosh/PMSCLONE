<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ImportTemplateController extends Controller
{
  public function create(Project $project)
  {
    $templates = auth()->user()->projectTemplates()->pluck('name', 'id')->prepend('Select template', '');
    return $this->sendRes('success', ['view_data' => view('admin.pages.projects.templates.import', compact('templates', 'project'))->render()]);
  }

  public function store(Request $request, Project $project)
  {
    $request->validate([
      'template' => 'required|exists:project_templates,id'
    ]);

    $template = auth()->user()->projectTemplates()->with('taskTemplates')->findOrFail($request->template);

    if($template->taskTemplates->count() == 0){
      return $this->sendErr('Template does not have any tasks');
    }

    foreach($template->taskTemplates as $taskTemplate){
      if($project->tasks()->where('subject', $taskTemplate->subject)->count() > 0){
        continue;
      }
      $task = $project->tasks()->create(['admin_id' => auth()->id(), 'status' => 'Not Started'] + $taskTemplate->toArray());
      if($taskTemplate->checkItemTemplates->count() > 0){
        foreach($taskTemplate->checkItemTemplates as $checkItemTemplate){
          $task->checklistItems()->create([
            'completed_by' => null,
            'assigned_to' => null,
            'due_date' => null,
            'created_by' => auth()->id()
            ] + $checkItemTemplate->toArray());
        }
      }
    }


    return $this->sendRes('Template imported successfully', ['event' => 'page_reload']);
  }
}
