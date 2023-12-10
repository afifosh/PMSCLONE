<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStoreRequest;
use App\Http\Requests\Admin\ProjectUpdateRequest;
use App\Models\Company;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectCategory;

class ProjectController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ProjectsDataTable $dataTable)
  {
    $summary = Project::selectRaw('status, COUNT(*) as project_count')
      ->whereIn('status', [0, 1, 2, 3, 4]) // 0: not started, 1: in progress, 2: on hold, 3: cancelled, 4: completed
      ->groupBy('status')
      ->get();
    return $dataTable->render('admin.pages.projects.index', ['summary' => $summary]);
    // view('admin.pages.projects.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['statuses'] = Project::STATUSES;
    $data['project'] = new Project;

    return view('admin.pages.projects.create', $data);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProjectStoreRequest $request)
  {
    $project = Project::create(['is_progress_calculatable' => $request->boolean('is_progress_calculatable')] + $request->validated());

    $project->members()->sync(filterInputIds($request->members));
    $project->companies()->sync(filterInputIds($request->companies));

    $project->createLog('Project Created', $project->toArray());

    return $this->sendRes('Created Successfully', ['event' => 'redirect', 'url' => route('admin.projects.index')]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    return view('admin.pages.projects.show', ['project' => $project]);
  }

  public function ganttChart(Project $project)
  {
    abort_if(!$project->isMine(), 403);
    $data['project'] = $project;
    $project->load(['contracts' => function($q){
      $q->where('status', '!=', 'Draft')->select('contracts.id', 'contracts.subject', 'contracts.project_id', 'contracts.status', 'contracts.start_date', 'contracts.end_date', 'assignable_type', 'assignable_id');
    }, 'contracts.phases' => function ($q) {
      $q->select('id', 'name', 'start_date', 'due_date', 'contract_id');
    }, 'contracts.assignable', 'contracts.project' => function ($q) {
      $q->select('id', 'name');
    }]);
    $data['ganttProjects'] = $project->contracts;

    return view('admin.pages.projects.gantt-chart', $data);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Project $project)
  {
    $project->load(['members', 'companies']);
    $data['programs'] = $project->program_id ? Program::where('id', $project->program_id)->pluck('name', 'id') : [];
    $data['categories'] = $project->category_id ? ProjectCategory::pluck('name', 'id') : [];
    $data['statuses'] = Project::STATUSES;
    $data['members'] = $project->members;
    $data['companies'] = $project->companies;
    $data['project'] = $project;
    return view('admin.pages.projects.create', $data);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(ProjectUpdateRequest $request, Project $project)
  {
    abort_if(!$project->isMine(), 403);

    $project->update(['is_progress_calculatable' => $request->boolean('is_progress_calculatable')] + $request->validated());

    $project->members()->sync(filterInputIds($request->members));
    $project->companies()->sync(filterInputIds($request->companies));

    $project->createLog('Project Updated', $project->toArray());

    return $this->sendRes('Updated Successfully', ['event' => 'redirect', 'url' => route('admin.projects.index')]);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Project $project)
  {
    abort_if(!$project->isMine(), 403);

    if ($project->group)
      $project->group->delete();

    $project->delete();

    $project->createLog('Project Deleted', $project->toArray());

    return $this->sendRes('Deleted Successfully', ['event' => 'table_reload', 'table_id' => 'projects-table']);
  }

  public function getCompanyByProject()
  {
    $data = Company::whereHas('projects', function($q){
      $q->where('projects.id', request()->id);
    })->pluck('name', 'companies.id')->prepend('Select Company', '');

    return $this->sendRes('Company', ['data' => $data]);
  }
}
