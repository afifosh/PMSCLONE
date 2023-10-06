<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class MergeInvoiceController extends Controller
{
  public function create(Invoice $invoice)
  {
    $invoice->load('contract');
    // Invoices that can be merged with the given invoice
    $data['invoices'] = Invoice::where('contract_id', $invoice->contract_id)
      ->where('id', '!=', $invoice->id)
      ->where('company_id', $invoice->company_id)
      ->where('type', $invoice->type)
      ->where('status', 'Draft')
      ->has('items')
      ->get();
    $data['invoice'] = clone $invoice;

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.merge-invoices', $data)->render()]);
  }

  public function store(Invoice $invoice, Request $request)
  {
    $request->validate([
      'invoice_ids' => 'required|array',
      'invoice_ids.*' => 'required|exists:invoices,id',
    ],[
      'invoice_ids.*.required' => __('Required'),
      'invoice_ids.*.exists' => __('One of the selected invoices is invalid'),
    ]);

    $invoices = Invoice::where('contract_id', $invoice->contract_id)
      ->where('id', '!=', $invoice->id)
      ->where('company_id', $invoice->company_id)
      ->where('type', $invoice->type)
      ->where('status', 'Draft')
      ->whereIn('id', $request->invoice_ids)
      ->has('items')
      ->get();

    $deleteMerged = !$request->boolean('cancel_merged');

    $invoice->mergeInvoices($invoices, $deleteMerged);

    return $this->sendRes('Invoices Merged Successfully', ['event' => 'page_reload', 'close' => 'globalModal']);
  }
}
