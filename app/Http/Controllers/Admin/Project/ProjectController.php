<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStoreRequest;
use App\Http\Requests\Admin\ProjectUpdateRequest;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Modules\Chat\Models\Group;
use Modules\Chat\Repositories\GroupRepository;

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
    $data['programs'] = Program::pluck('name', 'id')->prepend('Select Program', '');
    $data['categories'] = ProjectCategory::pluck('name', 'id')->prepend('Select Category', '');
    $data['statuses'] = Project::STATUSES;
    $data['members'] = Admin::get();
    $data['companies'] = Company::orderBy('id', 'desc')->pluck('name', 'id')->prepend('Select Company', '');
    $data['project'] = new Project;

    return view('admin.pages.projects.create', $data);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProjectStoreRequest $request, GroupRepository $groupRepository)
  {
    $project = Project::create(['is_progress_calculatable' => $request->boolean('is_progress_calculatable')] + $request->validated());
    $project->members()->sync($request->members);

    if ($request->boolean('create_chat_group'))
      $this->createGroupForProject($project, $groupRepository);

    $project->createLog('Project Created', $project->toArray());

    return $this->sendRes('Created Successfully', ['event' => 'redirect', 'url' => route('admin.projects.index')]);
  }

  protected function createGroupForProject(Project $project, GroupRepository $groupRepository)
  {
    $group = $groupRepository->storeForProject([
      'name' => $project->name,
      'project_id' => $project->id,
      'description' => $project->description,
      'group_type' => Group::TYPE_OPEN,
      'privacy' => Group::PRIVACY_PRIVATE,
      'created_by' => getLoggedInUserId(),
      'users' => $project->members->pluck('id')->toArray(),
    ]);

    return $group;
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
    $data['contracts'] = Contract::where('project_id', $project->id)->with('phases')->get();

    return view('admin.pages.projects.gantt-chart', $data);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Project $project)
  {
    $data['programs'] = Program::pluck('name', 'id')->prepend('Select Program', '');
    $data['categories'] = ProjectCategory::pluck('name', 'id')->prepend('Select Category', '');
    $data['statuses'] = Project::STATUSES;
    $data['members'] = Admin::get();
    $data['companies'] = Company::orderBy('id', 'desc')->pluck('name', 'id')->prepend('Select Company', '');
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
    $project->members()->sync($request->members);

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

  public function getByCompany(Request $request)
  {
    $data = Project::where('company_id', $request->id)->pluck('name', 'id')->prepend('Select Project', '');
    return $this->sendRes('Departments list', ['data' => $data]);
  }
}
