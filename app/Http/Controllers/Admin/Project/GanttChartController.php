<?php

namespace App\Http\Controllers\Admin\Project;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Project;
use Illuminate\Http\Request;

class GanttChartController extends Controller
{
  public function index()
  {
    $contracts = Contract::whereHas('project', function ($query) {
      $query->mine();
    })->when(request()->projects, function($q){
      $q->whereIn('project_id', request()->projects);
    })
    ->when(request()->status, function($q){
      $q->where('status', request()->status);
    })
    ->when(request()->companies, function($q){
      $q->whereIn('company_id', request()->companies);
    })->with('phases')->get();

    if(request()->ajax()){
      return response()->json($contracts);
    }
    $statuses = Contract::getPossibleEnumValues('status');

    $projects = Project::mine()->whereHas('contracts')->pluck('name', 'id');

    $companies = Company::whereHas('projects' , function($q){
      $q->mine();
    })->pluck('name', 'id');

    return view('admin.pages.projects.gantt-chart', compact('contracts', 'statuses', 'projects', 'companies'));
  }
}
