<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\DataTables\Admin\Invoice\InvoicesDataTable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceStoreRequest;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
  public function index(InvoicesDataTable $dataTable, null|string $model = null)
  {
    $data = [];

    if(request()->route()->getName() == 'admin.contracts.invoices.index'){
      $dataTable->filterBy = Contract::findOrFail(request()->route('contract'));
      $data['contract'] = $dataTable->filterBy;
    }
    elseif(request()->route()->getName() == 'admin.companies.invoices.index'){
      $dataTable->filterBy = Company::findOrFail(request()->route('company'));
      $data['company'] = $dataTable->filterBy;
    }else {
      $data['summary'] = Invoice::selectRaw('SUM(total) as total, SUM(paid_amount) as paid_amount, SUM(total - paid_amount)/100 as due_amount')->first();
      $data['overdue'] = Invoice::where('due_date', '<', now())->where('status', '!=', 'Paid')->count();
      $data['contracts'] = Contract::has('invoices')->pluck('subject', 'id')->prepend('All', '');
      $data['companies'] = Company::has('contracts.invoices')->get(['name', 'id', 'type'])->prepend('All', '');
    }

    return $dataTable->render('admin.pages.invoices.index', $data);
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

    return $this->sendRes('Invoice Added Successfully', ['event' => 'redirect', 'url' => route('admin.invoices.edit', $invoice)]);
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
    }elseif($request->update_discount){
      if($request->discount_type == 'Percentage'){
        $data['discount_amount'] = $invoice->subtotal * $request->discount_value / 100;
        $data['discount_percentage'] = $request->discount_value;
      }else{
        $data['discount_amount'] = $request->discount_value;
        $data['discount_percentage'] = 0;
      }
      $invoice->update($data);

      return $this->sendRes('Discount updated successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    }elseif($request->update_adjustment){
      $invoice->update($request->validated());

      return $this->sendRes('Adjustment updated successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    }

    $invoice->update(['status' => 'Sent'] + $request->validated());

    return $this->sendRes('Invoice Updated Successfully', ['event' => 'redirect', 'url' => route('admin.invoices.index')]);
  }

  public function destroy(Invoice $invoice)
  {
    $invoice->delete();

    return $this->sendRes('Invoice deleted successfully', ['event' => 'table_reload', 'table_id' => 'invoices-table']);
  }
}
