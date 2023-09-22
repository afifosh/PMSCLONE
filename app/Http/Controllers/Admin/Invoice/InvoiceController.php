<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\DataTables\Admin\Invoice\InvoicesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceStoreRequest;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function index(InvoicesDataTable $dataTable)
  {
    return $dataTable->render('admin.pages.invoices.index');
    // view('admin.pages.invoices.index');
  }

  public function create()
  {
    $data['invoice'] = new Invoice();

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.create', $data)->render()]);
  }

  public function store(InvoiceStoreRequest $request)
  {
    $invoice = Invoice::create($request->validated());

    return $this->sendRes('success', ['event' => 'redirect', 'url' => route('admin.invoices.edit', $invoice)]);
  }

  public function show(Invoice $invoice)
  {
    //
  }

  public function edit(Invoice $invoice)
  {
    $invoice->load('items.invoiceable', 'items.taxes');
    $data['invoice'] = $invoice;
    $data['tax_rates'] = Tax::get();

    return view('admin.pages.invoices.edit', $data);
  }

  public function update(InvoiceStoreRequest $request, Invoice $invoice)
  {
    if($request->update_tax_type){
      $invoice->update($request->validated());
      $invoice->taxes()->detach();
      $invoice->updateItemsTaxType();
      $invoice->updateTaxAmount();

      return back()->with('success', 'Tax type updated successfully');
    }

    $invoice->update(['status' => 'Sent'] + $request->validated());

    return $this->sendRes('success', ['event' => 'redirect', 'url' => route('admin.invoices.index')]);
  }

  public function destroy(Invoice $invoice)
  {
    $invoice->delete();

    return $this->sendRes('Invoice deleted successfully', ['event' => 'table_reload', 'table_id' => 'invoices-table']);
  }
}
