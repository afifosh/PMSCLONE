<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationsDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Applications\ApplicationStoreRequest;
use App\Models\Application;

class ApplicationController extends Controller
{
  public function index(ApplicationsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.index');
    // view('admin.pages.applications.index');
  }

  public function create()
  {
    $application = new Application();
    return view('admin.pages.applications.create', compact('application'));
  }

  public function store(ApplicationStoreRequest $request)
  {
    $application = Application::create($request->validated());
    $application->users()->sync($request->application_users);

    return $this->sendRes(__('Application created successfully'), ['event' => 'table_reload', 'table_id' => 'applications-table', 'close' => 'globalModal']);
  }

  public function edit(Application $application)
  {
    $data['application'] = $application;
    $application->load('program', 'type', 'category', 'pipeline', 'scorecard', 'company', 'users');

    abort_if(!auth()->user()->isSuperAdmin() && !$application->users->contains(auth()->id()), 403, __('Unauthorized'));

    $data['programs'] = [$application->program_id => $application->program->name];
    $data['types'] = [$application->type_id => $application->type->name];
    $data['categories'] = [$application->category_id => $application->category->name];
    $data['pipelines'] = [$application->pipeline_id => $application->pipeline->name];
    $data['scoreCards'] = [$application->scorecard_id => $application->scorecard->name];
    $data['companies'] = [$application->company_id => $application->company->name ?? ''];
    $data['users'] = $application->users->pluck('name', 'id')->toArray();
    $data['selectedUsers'] = $application->users->pluck('id')->toArray();

    return view('admin.pages.applications.create', $data);
  }

  public function update(Application $application, ApplicationStoreRequest $request)
  {
    abort_if(!auth()->user()->isSuperAdmin() && !$application->users->contains(auth()->id()), 403, __('Unauthorized'));

    $application->update($request->validated());
    $application->users()->sync($request->application_users);

    return $this->sendRes(__('Application updated successfully'), ['event' => 'table_reload', 'table_id' => 'applications-table', 'close' => 'globalModal']);
  }

  public function destroy(Application $application)
  {
    abort_if(!auth()->user()->isSuperAdmin() && !$application->users->contains(auth()->id()), 403, __('Unauthorized'));
    $application->delete();

    return $this->sendRes(__('Application deleted successfully'), ['event' => 'table_reload', 'table_id' => 'applications-table']);
  }
}
