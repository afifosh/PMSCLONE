<?php

namespace App\Http\Controllers\Admin\Project;

use App\DataTables\Admin\Project\ProjectsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectStoreRequest;
use App\Models\Admin;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(ProjectsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.projects.index');
    // view('admin.pages.projects.index');
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    $data['programs'] = Program::pluck('name', 'id')->prepend('Select Program', '');
    $data['categories'] = ProjectCategory::pluck('name', 'id')->prepend('Select Category', '');
    $data['statuses'] = ['Active'];
    $data['members'] = Admin::pluck('email', 'id');
    return view('admin.pages.projects.create', $data);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(ProjectStoreRequest $request)
  {
    $project = Project::create($request->validated());
    $project->members()->sync($request->members);
    return $this->sendRes('Created Successfully', ['event' => 'redirect', 'url' => route('admin.projects.index')]);
  }

  /**
   * Display the specified resource.
   */
  public function show(Project $project)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Project $project)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Project $project)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Project $project)
  {
    //
  }
}
