<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationCategoriesDataTable;
use App\Http\Controllers\Controller;

class ApplicationCategoryController extends Controller
{
  public function index(ApplicationCategoriesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.categories.index');
    // view('admin.pages.applications.categories.index');
  }
}
