<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\Dashboard\TasksDataTable;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;

class DashboardController extends Controller
{
    public function show(TasksDataTable $dataTable)
    {
      $data['projects'] = Project::whereHas('tasks.assignees', function($q){
        $q->where('admin_id', auth()->id());
      })->pluck('name', 'id');

      $data['task_statuses'] = Task::whereHas('assignees', function($q){
        $q->where('admin_id', auth()->id());
      })->pluck('status', 'status');

      $data['projectsStatusesChartData'] = Project::getProjectsStatusesChartData();
      return $dataTable->render('admin.pages.dashboard.show', $data);
      // return view('admin.pages.dashboard.show');
    }
}
