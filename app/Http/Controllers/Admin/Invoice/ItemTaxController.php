<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\ItemTaxStoreRequest;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;
use App\Models\InvoiceTax;

class ItemTaxController extends Controller
{
  public function create(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    $invoiceItem->load('invoiceable');
    $data['invoiceItem'] = $invoiceItem;
    $data['tax_rates'] = InvoiceConfig::whereIn('config_type', ['Tax', 'Down Payment'])->activeOnly()->get();
    $data['invoice'] = $invoice;
    if ($invoiceItem->invoiceable_type == 'App\Models\CustomInvoiceItem') {
      request()->merge(['item' => 'custom']);
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function store(Invoice $invoice, InvoiceItem $invoiceItem, ItemTaxStoreRequest $request)
  {
    $invoiceItem->taxes()->attach($request->tax->id, [
      'calculated_amount' => moneyToInt($request->calculated_tax_amount),
      'manual_amount' => moneyToInt($request->manual_tax_amount),
      'pay_on_behalf' => $request->pay_on_behalf,
      'is_authority_tax' => $request->is_authority_tax,
      'amount' => $request->tax->getRawOriginal('amount'),
      'type' => $request->tax->type,
      'is_simple_tax' => $request->tab == 'summary',
      'invoice_item_id' => $invoiceItem->id,
      'invoice_id' => $invoiceItem->invoice_id
    ]);

    if ($invoiceItem->deduction && $invoiceItem->deduction->is_before_tax) {
      $invoiceItem->deduction->update(['manual_amount' => 0]);
    }

    $invoiceItem->reCalculateTotal();
    $invoice->reCalculateTotal();

    return $this->sendRes('Tax Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, InvoiceItem $invoiceItem, $tax)
  {
    InvoiceTax::where('id', $tax)->delete();

    $invoiceItem->reCalculateTotal();
    $invoice->reCalculateTotal();

    return $this->sendRes('Tax Removed Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }
}
