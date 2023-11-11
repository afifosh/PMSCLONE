<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\CustomInvoiceItemStoreRequest;
use App\Http\Requests\Admin\Invoice\CustomInvoiceItemUpdateRequest;
use App\Models\CustomInvoiceItem;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class CustomInvoiceItemController extends Controller
{
  public function create(Invoice $invoice)
  {
    $customInvoiceItem = new CustomInvoiceItem();

    $tax_rates = InvoiceConfig::whereIn('config_type', ['Tax', 'Down Payment'])->activeOnly()->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.custom-items.create', compact('invoice', 'customInvoiceItem', 'tax_rates'))->render()]);
  }

  public function store(Invoice $invoice, CustomInvoiceItemStoreRequest $request)
  {
    $customItem = CustomInvoiceItem::create($request->validated() + ['invoice_id' => $invoice->id]);

    $item = $invoice->items()->create([
      'invoiceable_id' => $customItem->id,
      'invoiceable_type' => CustomInvoiceItem::class,
      'subtotal' => $customItem->subtotal,
      'total_tax_amount' => $request->total_tax_amount,
      'manual_tax_amount' => $request->manual_tax_amount,
      'total' => $request->total,
      'rounding_amount' => $request->rounding_amount,
    ]);

    if($request->deduct_downpayment)
    $item->deduction()->create([
      'deductible_id' => $item->id,
      'deductible_type' => InvoiceItem::class,
      'downpayment_id' => $request->downpayment_id,
      'dp_rate_id' => $request->dp_rate_id,
      'is_percentage' => $request->deduction_rate_type != 'Fixed',
      'amount' => $request->downpayment_amount,
      'manual_amount' => $request->manual_deduction_amount,
      'percentage' => $request->downpayment_rate->amount ?? 0,
      'is_before_tax' => $request->is_before_tax,
      'calculation_source' => $request->calculation_source,
    ]);

    $item->syncTaxes($request->taxes);

    $invoice->reCalculateTotal();

    return $this->sendRes('Item Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function edit(Invoice $invoice, CustomInvoiceItem $customInvoiceItem)
  {
    $tax_rates = InvoiceConfig::whereIn('config_type', ['Tax', 'Down Payment'])->activeOnly()->get();
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
      'rounding_amount' => moneyToInt($request->rounding_amount),
    ]);

    if($request->deduct_downpayment)
     $customInvoiceItem->invoiceItem->deduction()->updateOrCreate([
        'deductible_id' => $customInvoiceItem->invoiceItem->id,
        'deductible_type' => InvoiceItem::class,
      ], [
        'deductible_id' => $customInvoiceItem->invoiceItem->id,
        'deductible_type' => InvoiceItem::class,
        'downpayment_id' => $request->downpayment_id,
        'dp_rate_id' => $request->dp_rate_id,
        'is_percentage' => $request->deduction_rate_type != 'Fixed',
        'amount' => $request->downpayment_amount,
        'manual_amount' => $request->manual_deduction_amount,
        'percentage' => $request->downpayment_rate->amount ?? 0,
        'is_before_tax' => $request->is_before_tax,
        'calculation_source' => $request->calculation_source,
      ]);
      else{
        if(@$customInvoiceItem->invoiceItem->deduction)
        $customInvoiceItem->invoiceItem->deduction->delete();
      }

    $customInvoiceItem->invoiceItem->syncTaxes($request->taxes);

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
