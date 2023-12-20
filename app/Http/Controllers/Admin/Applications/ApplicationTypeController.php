<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationTypesDataTable;
use App\Http\Controllers\Controller;

class ApplicationTypeController extends Controller
{
  public function index(ApplicationTypesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.types.index');
    //view('admin.pages.applications.types.index');
  }
}
