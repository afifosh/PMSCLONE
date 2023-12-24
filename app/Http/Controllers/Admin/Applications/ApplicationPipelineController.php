<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationPipelinesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Applications\Pipeline\PipelineStoreRequest;
use App\Models\ApplicationPipeline;
use Google\Service\TrafficDirectorService\Pipe;

class ApplicationPipelineController extends Controller
{
  public function index(ApplicationPipelinesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.pipelines.index');
    //view('admin.pages.applications.pipelines.index');
  }

  public function create()
  {
    $data['pipeline'] = new ApplicationPipeline();

    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.pipelines.create', $data)->render(), 'JsMethods' => ['initRepeater']]);
  }

  public function store(PipelineStoreRequest $request)
  {
    ApplicationPipeline::create($request->validated());

    return $this->sendRes('Pipeline created successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table', 'close' => 'globalModal']);
  }

  public function edit(ApplicationPipeline $pipeline)
  {
    $pipeline->load('stages');

    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.pipelines.create', compact('pipeline'))->render(), 'JsMethods' => ['initRepeater']]);
  }

  public function update(PipelineStoreRequest $request, ApplicationPipeline $pipeline)
  {
    $pipeline->update($request->validated());

    return $this->sendRes('Pipeline updated successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table', 'close' => 'globalModal']);
  }

  public function destroy(ApplicationPipeline $pipeline)
  {
    $pipeline->delete();

    return $this->sendRes('Pipeline deleted successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table']);
  }
}
