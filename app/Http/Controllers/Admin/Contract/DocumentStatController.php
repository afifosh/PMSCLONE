<?php

namespace App\Http\Controllers\Admin\Contract;

use App\DataTables\Admin\Contract\DocumentStatsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract;
use App\Models\ContractCategory;
use App\Models\ContractType;

class DocumentStatController extends Controller
{
  public function index(DocumentStatsDataTable $dataTable)
  {
    $data['contract_statuses'] = ['' => __('All')] + array_combine(Contract::STATUSES, Contract::STATUSES);
    $data['contract_categories'] = ContractCategory::pluck('name', 'id')->prepend('All', '');
    $data['contract_types'] = ContractType::pluck('name', 'id')->prepend('All', '');
    return $dataTable->render('admin.pages.contracts.document-stats.index', $data);

    // view('admin.pages.contracts.document-stats.index');
  }
}
