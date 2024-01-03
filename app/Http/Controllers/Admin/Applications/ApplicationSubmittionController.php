<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\SubmittionsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApplicationSubmittionController extends Controller
{
  public function index(SubmittionsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.submittions.index');
    // return view('admin.pages.applications.submittions.index');
  }
}
