<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GanttChartController extends Controller
{
  public function index()
  {
    $ganttProjects = Project::mine()->has('contracts')
      ->when(request()->projects, function ($q) {
        $q->where('id', request()->projects);
      })
      ->when(request()->has('status'), function ($q) {
        $q->where('status', request()->status);
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
          $ids[] = 'Contract:' . $contract->id;

          if($contract->phases->isEmpty()) continue;
          foreach ($contract->phases as $phase) {
            $ids[] = 'Phase:'.$phase->id;
          }
        }
      }
      return response()->json($ids);
    }
    $statuses = Project::STATUSES;

    $projects = Project::mine()->whereHas('contracts')->pluck('name', 'id')->prepend('All', '');

    $companies = Company::whereHas('projects', function ($q) {
      $q->mine();
    })->pluck('name', 'id')->prepend('All', '');

    return view('admin.pages.projects.gantt-chart', compact('ganttProjects', 'statuses', 'projects', 'companies'));
  }
}
