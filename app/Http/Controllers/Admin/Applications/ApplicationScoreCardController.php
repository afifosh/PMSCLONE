<?php

namespace App\Http\Controllers\Admin\Applications;

use App\DataTables\Admin\Applications\ApplicationScoreCardsDataTable;
use App\Http\Controllers\Controller;

class ApplicationScoreCardController extends Controller
{
  public function index(ApplicationScoreCardsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.applications.scorecards.index');
    //view('admin.pages.applications.scorecards.index');
  }
}
