<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\LogsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractStage;
use Illuminate\Http\Request;

class LogController extends Controller
{
  public function index(Contract $contract, LogsDataTable $dataTable)
  {
    $dataTable->contract = $contract;
    return $dataTable->render('admin.pages.contracts.logs.index', compact('contract'));
    // view('admin.pages.contracts.logs.index');
  }
}
