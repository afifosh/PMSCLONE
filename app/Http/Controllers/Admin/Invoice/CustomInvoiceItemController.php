<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\CustomInvoiceItemStoreRequest;
use App\Http\Requests\Admin\Invoice\CustomInvoiceItemUpdateRequest;
use App\Models\CustomInvoiceItem;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class CustomInvoiceItemController extends Controller
{
  public function create(Invoice $invoice)
  {
    $customInvoiceItem = new CustomInvoiceItem();

    $tax_rates = Tax::where('is_retention', 0)->where('status', 'Active')->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.custom-items.create', compact('invoice', 'customInvoiceItem', 'tax_rates'))->render()]);
  }

  public function store(Invoice $invoice, CustomInvoiceItemStoreRequest $request)
  {
    $customItem = CustomInvoiceItem::create($request->validated() + ['invoice_id' => $invoice->id]);

    $item = $invoice->items()->create([
      'invoiceable_id' => $customItem->id,
      'invoiceable_type' => CustomInvoiceItem::class,
      'subtotal' => $customItem->subtotal,
      'downpayment_id' => $request->downpayment_id,
      'downpayment_amount' => $request->downpayment_amount,
      'total_tax_amount' => $request->total_tax_amount,
      'manual_tax_amount' => $request->manual_tax_amount,
      'total' => $request->total,
    ]);

    $item->syncTaxes($request->taxes);

    $invoice->reCalculateTotal();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function edit(Invoice $invoice, CustomInvoiceItem $customInvoiceItem)
  {
    $tax_rates = Tax::where('is_retention', 0)->where('status', 'Active')->get();
    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.custom-items.create', compact('invoice', 'customInvoiceItem', 'tax_rates'))->render()]);
  }

  public function update(Invoice $invoice, CustomInvoiceItem $customInvoiceItem, CustomInvoiceItemUpdateRequest $request)
  {
    $customInvoiceItem->update($request->validated());

    $customInvoiceItem->invoiceItem()->update([
      'subtotal' => moneyToInt($customInvoiceItem->subtotal),
      'total_tax_amount' => moneyToInt($request->total_tax_amount),
      'manual_tax_amount' => moneyToInt($request->manual_tax_amount),
      'total' => moneyToInt($request->total),
    ]);

    $invoice->reCalculateTotal();

    return $this->sendRes('Item Updated Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, CustomInvoiceItem $customInvoiceItem)
  {
    $customInvoiceItem->delete();

    $invoice->reCalculateTotal();

    return $this->sendRes('Item Deleted Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }
}
