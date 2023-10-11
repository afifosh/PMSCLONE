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
    $data['is_editable'] = $invoice->isEditable();

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
    if(!$invoice->isEditable()){
      return $this->sendError('Invoice is not editable');
    }

    $phases = filterInputIds($request->phases);

    $invoice->attachPhasesWithTax($phases);

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, $invoiceItem, Request $request)
  {
    if(!$invoice->isEditable()){
      return $this->sendError('Invoice is not editable');
    }

    $request->validate([
      'ids' => 'required|array',
      'ids.*' => 'required|exists:invoice_items,id'
    ]);

    $items = $invoice->items()->whereIn('invoice_items.id', $request->ids)->get();

    $items->each(function($item){
      if ($item->invoiceable_type == CustomInvoiceItem::class)
        $item->invoiceable->delete();

      $item->taxes()->detach();
      $item->delete();
    });

    $invoice->updateTaxAmount();

    return $this->sendRes('Item Removed', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
