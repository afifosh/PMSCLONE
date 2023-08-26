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
    $ganttProjects = Project::mine()
      ->when(request()->projects, function ($q) {
        $q->where('id', request()->projects);
      })
      ->when(request()->companies, function ($q) {
        $q->where('company_id', request()->companies);
      })
      ->when(request()->search_q, function ($q) {
        $q->where('name', 'like', '%' . request()->search_q . '%')->orWhereHas('contracts', function ($q) {
          $q->where('subject', 'like', '%' . request()->search_q . '%');
        })->orWhereHas('contracts.phases', function ($q) {
          $q->where('name', 'like', '%' . request()->search_q . '%');
        });
      })
      ->when(request()->status, function ($q) {
        $q->whereHas('contracts', function ($q) {
          if (request()->status == 'Not started') {
            $q->where('start_date', '>', now());
          } elseif (request()->status == 'Active') {
            $q->where('start_date', '<=', now())->where('end_date', '>=', now())->where('end_date', '<', now()->subWeeks(2));
          } elseif (request()->status == 'About To Expire') {
            $q->where('end_date', '>=', now()->subWeeks(2))->where('end_date', '>', now());
          } elseif (request()->status == 'Expired') {
            $q->where('end_date', '<', now());
          } elseif (request()->status == 'Terminated') {
            $q->where('status', 'Terminated');
          } elseif (request()->status == 'Paused') {
            $q->where('status', 'Paused');
          }
        });
      })
      ->with(['contracts' => function ($q) {
        $q->select('contracts.id', 'contracts.subject', 'contracts.project_id', 'contracts.status', 'contracts.start_date', 'contracts.end_date');
      }, 'contracts.phases' => function ($q) {
        $q->select('id', 'name', 'start_date', 'due_date', 'contract_id');
      }])
      ->select('projects.id', 'projects.name')->get();

    if (request()->ajax()) {
      return response()->json($ganttProjects);
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
