<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class GanttChartController extends Controller
{
  public function index()
  {
    $projects = Project::mine()->with('contracts.phases')->get();

    $tasks = [];

    if($projects->count() > 0) {
      foreach ($projects as $project) {
        $tasks[] = [
          'id' => 'project-'.$project->id,
          'name' => $project->name,
          'start' => $project->start_date->format('Y-m-d'),
          'end' => $project->deadline->format('Y-m-d'),
          'dependencies' => '',
          'collapsed' => false,
          'custom_class' => 'pro-bar'
        ];
        if($project->contracts->count() > 0)
        foreach ($project->contracts as $contract) {
          $tasks[] = [
            'id' => 'contract-'.$contract->id,
            'name' => $contract->subject,
            'start' => $contract->start_date->format('Y-m-d'),
            'end' => $contract->end_date->format('Y-m-d'),
            'dependencies' => 'project-'.$project->id,
            'collapsed' => false,
            'custom_class' => 'con-bar'
          ];

          if($contract->phases->count())
          foreach($contract->phases as $phase)
          {
            $tasks[] = [
              'id' => 'Phase'.$phase->id,
              'name' => $phase->name,
              'start' => $phase->start_date->format('Y-m-d'),
              'end' => $phase->due_date->format('Y-m-d'),
              'dependencies' => 'contract-'.$contract->id,
              'collapsed' => false,
              'custom_class' => 'pha-bar'
            ];
          }
        }
      }
    }

    return view('admin.pages.projects.gantt-chart', compact('tasks'));
  }
}
