<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationPipelinesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Applications\Pipeline\PipelineStoreRequest;
use App\Models\ApplicationPipeline;
use App\Models\ApplicationPipelineStage;
use Google\Service\TrafficDirectorService\Pipe;
use Illuminate\Support\Facades\DB;

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
    $pipeline = ApplicationPipeline::create($request->validated());

    $is_default_set = false;

    foreach ($request->stages as $i => $stage) {
      $pipeline->stages()->create(['is_default' => !$is_default_set && boolVal(@$stage['is_default'])] + $stage);
    }

    return $this->sendRes('Pipeline created successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table', 'close' => 'globalModal']);
  }

  public function edit(ApplicationPipeline $pipeline)
  {
    $pipeline->load('stages');

    return $this->sendRes('success', ['view_data' => view('admin.pages.applications.pipelines.create', compact('pipeline'))->render(), 'JsMethods' => ['initRepeater']]);
  }

  public function update(PipelineStoreRequest $request, ApplicationPipeline $pipeline)
  {
    DB::beginTransaction();
    try {
      $pipeline->update($request->validated());

      $is_default_set = false;
      foreach ($request->stages as $i => $stage) {
        $pipeline->stages()->updateOrCreate(['id' => @$stage['id']], ['is_default' => !$is_default_set && boolVal(@$stage['is_default']), 'order' => $i] + $stage);
      }

      // delete stages which are not in request
      $pipeline->stages()->whereNotIn('id', array_column($request->stages, 'id'))->delete();
      DB::commit();

      return $this->sendRes('Pipeline updated successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table', 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function destroy(ApplicationPipeline $pipeline)
  {
    if($pipeline->applications()->count() > 0) {
      return $this->sendError('Pipeline is in use');
    }
    $pipeline->stages()->delete();
    $pipeline->delete();

    return $this->sendRes('Pipeline deleted successfully', ['event' => 'table_reload', 'table_id' => 'application-pipelines-table']);
  }
}
