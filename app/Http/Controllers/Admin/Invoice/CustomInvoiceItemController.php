<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\CustomInvoiceItemStoreRequest;
use App\Models\CustomInvoiceItem;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CustomInvoiceItemController extends Controller
{
  public function create(Invoice $invoice)
  {
    $customInvoiceItem = new CustomInvoiceItem();

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.custom-items.create', compact('invoice', 'customInvoiceItem'))->render()]);
  }

  public function store(Invoice $invoice, CustomInvoiceItemStoreRequest $request)
  {
    $customItem = CustomInvoiceItem::create($request->validated() + ['invoice_id' => $invoice->id, 'total' => $request->price * $request->quantity]);

    $invoice->items()->create([
      'invoiceable_id' => $customItem->id,
      'invoiceable_type' => CustomInvoiceItem::class,
      'amount' => $customItem->total,
    ]);

    $invoice->reCalculateTotal();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function edit(Invoice $invoice, CustomInvoiceItem $customInvoiceItem)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.custom-items.create', compact('invoice', 'customInvoiceItem'))->render()]);
  }

  public function update(Invoice $invoice, CustomInvoiceItem $customInvoiceItem, Request $request)
  {
    $customInvoiceItem->update($request->validated() + ['total' => $request->price * $request->quantity]);

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
