<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceItemsStoreReqeust;
use App\Models\ContractPhase;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
  public function index(Invoice $invoice)
  {
    $data['invoice'] = $invoice;
    $data['tax_rates'] = Tax::get();

    if (request()->mode == 'edit') {
      $data['invoice']->load('items.invoiceable');

      return $this->sendRes('success', [
        'view_data' => view('admin.pages.invoices.items.edit-list', $data)->render(),
        'summary' => view('admin.pages.invoices.items.summary', $data)->render(),
        'balance_summary' => view('admin.pages.invoices.balance-summary', $data)->render(),
      ]);
    }

    return view('admin.pages.invoices.items.index', $data);
  }

  public function create(Invoice $invoice)
  {
    $data['invoice'] = $invoice;

    if (request()->type != 'retentions') {
      $data['phases'] = $invoice->contract->phases()->has('addedAsInvoiceItem', 0)->get();
    } else
      $data['invoices'] = Invoice::where('contract_id', $invoice->contract_id)
        ->where('id', '!=', $invoice->id)
        ->where('retention_amount', '!=', 0)
        ->has('addedAsInvoiceItem', 0)
        ->get();

    return $this->sendRes('success', ['view_data' => request()->type != 'retentions' ?
      view('admin.pages.invoices.items.create', $data)->render()
      : view('admin.pages.invoices.items.retentions-list', $data)->render()]);
  }

  public function store(InvoiceItemsStoreReqeust $request, Invoice $invoice)
  {
    if ($request->type == 'retentions')
      return $this->storeRetentions($request, $invoice);
    $phases = filterInputIds($request->phases);

    $pivot_amounts = ContractPhase::whereIn('id', $phases)
    ->where('contract_id', $invoice->contract_id)
    ->has('addedAsInvoiceItem', 0)
    ->pluck('estimated_cost', 'id')
    ->toArray();

    // formate data for pivot table
    $data = [];
    foreach ($phases as $phase) {
      $data[$phase] = ['amount' => $pivot_amounts[$phase] * 100]; // convert to cents manually, setter is not working for pivot table
    }

    $invoice->phases()->syncWithoutDetaching($data);
    $invoice->updateSubtotal();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function storeRetentions(InvoiceItemsStoreReqeust $request, Invoice $invoice)
  {
    $retentions = filterInputIds($request->retentions);

    $pivot_amounts = Invoice::whereIn('id', $retentions)
    ->where('contract_id', $invoice->contract_id)
    ->where('retention_amount', '!=', 0)
    ->has('addedAsInvoiceItem', 0)
    ->pluck('retention_amount', 'id')->toArray();

    // formate data for pivot table
    $data = [];
    foreach ($retentions as $retention) {
      $data[$retention] = ['amount' => -$pivot_amounts[$retention]]; // it was negative in db so make it positive
    }

    $invoice->retentions()->syncWithoutDetaching($data);

    $invoice->updateSubtotal();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, $invoiceItem)
  {
    $invoice->items()->where('id', $invoiceItem)->delete();
    $invoice->updateSubtotal();

    return $this->sendRes('Item Removed', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
