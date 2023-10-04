<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\InvoiceItemsStoreReqeust;
use App\Models\ContractPhase;
use App\Models\CustomInvoiceItem;
use App\Models\Invoice;
use App\Models\Tax;
use DataTables;
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

    if (request()->type == 'jsonData') {
      return DataTables::eloquent($invoice->contract->phases()->has('addedAsInvoiceItem', 0))->toJson();
    }

    return $this->sendRes('success', [
      'view_data' => view('admin.pages.invoices.items.create', $data)->render(), 'JsMethods' => ['initPhasesDataTable']
    ]);
  }

  public function store(InvoiceItemsStoreReqeust $request, Invoice $invoice)
  {
    $phases = filterInputIds($request->phases);

    $pivot_amounts = ContractPhase::whereIn('id', $phases)
      ->where('contract_id', $invoice->contract_id)
      ->has('addedAsInvoiceItem', 0)
      ->with('taxes')
      ->get();

    // formate data for pivot table
    $data = [];
    foreach ($phases as $phase) {
      $data[$phase] = [
        'amount' => $pivot_amounts->where('id', $phase)->first()->estimated_cost * 1000
      ]; // convert to cents manually, setter is not working for pivot table
    }

    $invoice->phases()->syncWithoutDetaching($data);

    foreach($pivot_amounts as $phase){
      $invPhase = $invoice->items()->where('invoiceable_id', $phase->id)->first();

      foreach($phase->taxes as $tax){
        $invPhase->taxes()->attach($tax->id, ['amount' => $tax->pivot->amount, 'type' => $tax->pivot->type, 'invoice_id' => $invoice->id]);
      }

      $invPhase->updateTaxAmount();
    }

    $invoice->updateTaxAmount();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, $invoiceItem)
  {
    $item = $invoice->items()->find($invoiceItem);
    if ($item->invoiceable_type == CustomInvoiceItem::class)
      $item->invoiceable->delete();

    $invoice->items()->where('id', $invoiceItem)->delete();

    $invoice->updateTaxAmount();

    return $this->sendRes('Item Removed', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
