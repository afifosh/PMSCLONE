<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\DataTables\Admin\Invoice\InvoicesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceStoreRequest;
use App\Models\Invoice;
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
    $invoice->load('items.invoiceable');

    $data['invoice'] = $invoice;

    return view('admin.pages.invoices.edit', $data);
  }

  public function update(InvoiceStoreRequest $request, Invoice $invoice)
  {
    $invoice->update($request->validated());

    return $this->sendRes('success', ['event' => 'redirect', 'url' => route('admin.invoices.index')]);
  }

  public function destroy(Invoice $invoice)
  {
    $invoice->delete();

    return $this->sendRes('Invoice deleted successfully', ['event' => 'table_reload', 'table_id' => 'invoices-table']);
  }
}
