<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Program;
use App\Models\Project;

class GanttChartController extends Controller
{
  public function index()
  {
    $ganttProjects = Contract::when(request()->projects, function ($q) {
      $q->where('project_id', request()->projects)->whereNotNull('project_id');
    })
      ->applyRequestFilters()
      ->with([
        'stages' => function ($q) {
          $q->select('contract_stages.id', 'contract_stages.name', 'start_date', 'due_date', 'contract_id');
        },
        'stages.phases' => function ($q) {
          $q->select('contract_phases.id', 'contract_phases.name', 'start_date', 'due_date', 'contract_id', 'stage_id');
        }, 'project' => function ($q) {
          $q->select('projects.id', 'name');
        }, 'assignable'
      ])
      ->select('contracts.id', 'contracts.subject', 'contracts.project_id', 'contracts.status', 'contracts.start_date', 'contracts.end_date', 'assignable_type', 'assignable_id')->get();

    if (request()->ajax()) {
      return response()->json($ganttProjects);
    }
    $statuses = Contract::STATUSES;
    $statuses = array_combine($statuses, $statuses);

    $projects = Project::has('contracts')->pluck('name', 'id')->prepend('All', '0');
    $contractTypes = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');

    $programs = Program::has('contracts')->pluck('name', 'id')->prepend('All', '0');

    return view('admin.pages.projects.gantt-chart', compact('ganttProjects', 'statuses', 'projects', 'contractTypes', 'programs'));
  }
}
