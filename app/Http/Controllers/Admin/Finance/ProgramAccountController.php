<?php

namespace App\Http\Controllers\Admin\Finance;

use App\DataTables\Admin\Finance\ProgramAccountsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgramAccountController extends Controller
{
  public function index(ProgramAccountsDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.finances.program-accounts.index');
    // view('admin.pages.finances.program-accounts.index')
  }
}
