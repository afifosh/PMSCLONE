<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class CommentController extends Controller
{
  public function __construct()
  {
    // check if invoice belongs to contract
    $this->middleware('verifyContractNotTempered:invoice,contract_id')->only(['index']);
  }

  public function index($contract, Invoice $invoice)
  {
    if($contract != $invoice->contract_id) {
      return $this->sendError( __('Invalid invoice'));
    }

    if(request()->tab == 'authority-tax'){
      $invoice = $invoice->authorityInvoice;
    }
    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.comments.index', compact('invoice'))->render(), 'JsMethods' => ['liveWireRescan']]);
  }
}
