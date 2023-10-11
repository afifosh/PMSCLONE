<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
      'invoice_ids.*' => 'nullable|exists:invoices,id',
    ],[
      'invoice_ids.*.required' => __(''),
      'invoice_ids.*.exists' => __('One of the selected invoices is invalid'),
    ]);

    $invoice_ids = filterInputIds($request->invoice_ids);
    if(count($invoice_ids) < 1) throw ValidationException::withMessages(['invoice_ids' => __('')]);

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
