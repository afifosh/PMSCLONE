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

    if (request()->route()->getName() == 'admin.contracts.invoices.index') {
      $dataTable->filterBy = Contract::findOrFail(request()->route('contract'));
      $data['contract'] = $dataTable->filterBy;
    } elseif (request()->route()->getName() == 'admin.companies.invoices.index') {
      $dataTable->filterBy = Company::findOrFail(request()->route('company'));
      $data['company'] = $dataTable->filterBy;
    } else {
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
    $invoice = Invoice::create($request->validated() + ['total' => $request->subtotal]);

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
    $data['tax_rates'] = Tax::get(); // both retention and taxes, will be filtered in view

    if($invoice->type != 'Regular'){
      return view('admin.pages.invoices.edit-downpayment', $data);
    }

    return view('admin.pages.invoices.edit', $data);
  }

  public function update(InvoiceStoreRequest $request, Invoice $invoice)
  {
    if ($request->update_tax_type) {
      $invoice->update($request->validated());
      $invoice->taxes()->detach();
      $invoice->updateItemsTaxType();
      $invoice->reCalculateTotal();

      return back()->with('success', 'Tax type updated successfully');
    } elseif ($request->update_discount) {
      if ($request->discount_type == 'Percentage') {
        $data['discount_amount'] = - ($invoice->subtotal * $request->discount_value) / 100; //store negative value
        $data['discount_percentage'] = $request->discount_value;
      } else {
        $data['discount_amount'] = -$request->discount_value; //store negative value
        $data['discount_percentage'] = 0;
      }
      $invoice->update($data + ['discount_type' => $request->discount_type]);
      $invoice->reCalculateTotal();

      return $this->sendRes('Discount updated successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } elseif ($request->update_adjustment) {
      $invoice->update($request->validated());
      $invoice->reCalculateTotal();

      return $this->sendRes('Adjustment updated successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } elseif ($request->update_retention) {
      // retention is basically deduction after applying discount, taxes and adjustments from the total. Which will be paid later on.
      $retenion = Tax::where('is_retention', true)->find($request->retention_id);
      if (!$retenion) {
        $data['retention_amount'] = 0;
        $data['retention_percentage'] = 0;
      } elseif ($retenion->type == 'Percent') {
        $data['retention_amount'] = - (($invoice->total) * $retenion->amount) / 100;
        $data['retention_percentage'] = $retenion->amount;
      } else {
        $data['retention_amount'] = -$retenion->amount;
        $data['retention_percentage'] = 0;
      }
      // if($request->retention_type == 'Percentage'){
      //   $data['retention_amount'] = -($invoice->subtotal * $request->retention_value) / 100;
      //   $data['retention_percentage'] = $request->retention_value;
      // }else{
      //   $data['retention_amount'] = -$request->retention_value;
      //   $data['retention_percentage'] = 0;
      // }
      $invoice->update($data + ['retention_id' => $request->retention_id, 'retention_name' => $retenion->name ?? null]);
      $invoice->reCalculateTotal();

      return $this->sendRes('Retention updated successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    }elseif($request->type == 'downpayment'){
      $invoice->update($request->validated() + ['total' => $request->subtotal]);

      return $this->sendRes('Invoice Updated Successfully', ['event' => 'page_reload']);
    }

    $invoice->update(['status' => 'Sent'] + $request->validated());

    return $this->sendRes('Invoice Updated Successfully', ['event' => 'redirect', 'url' => route('admin.invoices.index')]);
  }

  public function destroy(Invoice $invoice)
  {
    $invoice->delete();

    return $this->sendRes('Invoice deleted successfully', ['event' => 'table_reload', 'table_id' => 'invoices-table']);
  }

  public function sortItems(Invoice $invoice, Request $request)
  {
    $request->validate([
      'items' => 'required|array',
      'items.*' => 'required|integer|exists:invoice_items,id,invoice_id,' . $invoice->id,
    ]);

    foreach($request->items as $order => $item_id){
      $invoice->items()->where('id', $item_id)->update(['order' => $order]);
    }

  }
}
