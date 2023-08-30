<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractType;
use App\Models\Project;

class GanttChartController extends Controller
{
  public function index()
  {
    $ganttProjects = Contract::where('status', '!=', 'Draft')->whereNotNull('start_date')->whereNotNull('end_date')->when(request()->projects, function ($q) {
      $q->where('project_id', request()->projects)->whereNotNull('project_id');
    })
      ->when(request()->companies, function ($q) {
        $q->where('assignable_type', Company::class)->where('assignable_id', request()->companies);
      })
      ->when(request()->contract_client, function ($q) {
        $q->where('assignable_type', Client::class)->where('assignable_id', request()->contract_client);
      })
      ->when(request()->search_q, function ($q) {
        $q->where(function ($q) {
          $q->where('subject', 'like', '%' . request()->search_q . '%')
            ->orWhereHas('phases', function ($q) {
              $q->where('name', 'like', '%' . request()->search_q . '%');
            });
        });
      })
      ->when(request()->status, function ($q) {
        if (request()->status == 'Not started') {
          $q->where('start_date', '>', now());
        } elseif (request()->status == 'Expired') {
          $q->where('end_date', '<', now());
        } elseif (request()->status == 'Terminated') {
          $q->where('status', 'Terminated');
        } elseif (request()->status == 'Paused') {
          $q->where('status', 'Paused');
        } elseif (request()->status == 'Active') {
          $q->where('status', 'Active')->where('start_date', '<=', now())->where('end_date', '>=', now())->where('end_date', '>=', now()->addWeeks(2));
        } elseif (request()->status == 'About To Expire') {
          $q->where('status', 'Active')->where('end_date', '>', now())->where('end_date', '<', now()->addWeeks(2));
        }
      })
      ->when(request()->contract_type, function ($q) {
        $q->whereHas('type', function ($q) {
          $q->where('id', request()->contract_type);
        });
      })
      ->with(['phases' => function ($q) {
        $q->select('id', 'name', 'start_date', 'due_date', 'contract_id');
      }, 'project' => function ($q) {
        $q->select('id', 'name');
      }, 'assignable'])
      ->select('contracts.id', 'contracts.subject', 'contracts.project_id', 'contracts.status', 'contracts.start_date', 'contracts.end_date', 'assignable_type', 'assignable_id')->get();

    if (request()->ajax()) {
      return response()->json($ganttProjects);
    }
    $statuses = Contract::STATUSES;
    $statuses = array_combine($statuses, $statuses);

    $projects = Project::mine()->whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');
    $contractTypes = ContractType::whereHas('contracts')->pluck('name', 'id')->prepend('All', '0');
    $contractClients = Client::whereHas('contracts')->pluck('email', 'id')->prepend('All', '0');

    $companies = Company::has('contracts')->pluck('name', 'id')->prepend('All', '0');

    return view('admin.pages.projects.gantt-chart', compact('ganttProjects', 'statuses', 'projects', 'companies', 'contractTypes', 'contractClients'));
  }
}
