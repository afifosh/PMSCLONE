<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\DataTables\Admin\Invoice\TaxAuthorityInvoicesDataTable;
use App\Http\Controllers\Controller;
use App\Models\AuthorityInvoice;
use App\Models\Invoice;
use Illuminate\Http\Request;

class TaxAuthorityInvoiceController extends Controller
{
  public function index(TaxAuthorityInvoicesDataTable $dataTable)
  {
    $data = [];
    $data['summary'] = AuthorityInvoice::selectRaw('
        SUM(total) / 1000 as total_amount,
        SUM(paid_amount) as paid_amount,
        SUM(total - paid_amount) / 1000 as due_amount,
        COUNT(*) as total_invoices,
        SUM(IF(status = "Paid", 1, 0)) as paid,
        SUM(IF((status = "Unpaid" OR status = "Draft" OR status = "Sent") AND (due_date > NOW()), 1, 0)) as unpaid,
        SUM(IF(status = "Partially Paid", 1, 0)) as partially_paid,
        SUM(IF(due_date < NOW() AND status != "Paid", 1, 0)) as overdue
      ')->first();
    $data['trashed_count'] = AuthorityInvoice::onlyTrashed()->count();
    $data['invoice_statuses'] = ['' => __('All')] + array_combine(AuthorityInvoice::STATUSES, AuthorityInvoice::STATUSES);

    return $dataTable->render('admin.pages.invoices.tax-authority.index', $data);
    // view('admin.pages.invoices.tax-authority.index')
  }

  public function show(AuthorityInvoice $taxAuthorityInvoice)
  {
    if (request()->json) {
      return response()->json($taxAuthorityInvoice);
    }
  }
}