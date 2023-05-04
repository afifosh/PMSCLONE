<?php

namespace App\Http\Controllers\Company;

use App\DataTables\Company\Profile\ApprovalRequestsDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApprovalRequestController extends Controller
{
    public function index(ApprovalRequestsDataTable $dataTable)
    {
      return $dataTable->render('pages.company-profile.approval-requests.index');
      // view('pages.company-profile.approval-requests.index');
    }
}
