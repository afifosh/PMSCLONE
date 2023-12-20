<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationPipelinesDataTable;
use App\Http\Controllers\Controller;

class ApplicationPipelineController extends Controller
{
  public function index(ApplicationPipelinesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.pipelines.index');
    //view('admin.pages.applications.pipelines.index');
  }
}
