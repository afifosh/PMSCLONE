<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\DocumentStatsDataTable;
use App\Http\Controllers\Controller;

class DocumentStatController extends Controller
{
  public function index(DocumentStatsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.contracts.document-stats.index');
    view('admin.pages.contracts.document-stats.index');
  }
}
