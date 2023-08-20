<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Project;

class GanttChartController extends Controller
{
  public function index()
  {
    $ganttProjects = Project::mine()->has('contracts')
      ->when(request()->projects, function ($q) {
        $q->where('id', request()->projects);
      })
      ->when(request()->companies, function ($q) {
        $q->where('company_id', request()->companies);
      })
      ->with('contracts.phases')->get();

    if (request()->ajax()) {
      $ids = [];
      foreach ($ganttProjects as $project) {
        $ids[] = 'Project:' . $project->id;

        if($project->contracts->isEmpty()) continue;
        foreach ($project->contracts as $contract) {
          if(request()->status && $contract->status != request()->status) continue;
          $ids[] = 'Contract:' . $contract->id;

          if($contract->phases->isEmpty()) continue;
          foreach ($contract->phases as $phase) {
            $ids[] = 'Phase:'.$phase->id;
          }
        }
      }
      return response()->json($ids);
    }
    $statuses = Contract::STATUSES;
    $statuses = array_combine($statuses, $statuses);

    $projects = Project::mine()->whereHas('contracts')->pluck('name', 'id')->prepend('All', '');

    $companies = Company::whereHas('projects', function ($q) {
      $q->mine();
    })->pluck('name', 'id')->prepend('All', '');

    return view('admin.pages.projects.gantt-chart', compact('ganttProjects', 'statuses', 'projects', 'companies'));
  }
}
