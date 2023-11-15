<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\ItemDeductionStoreRequest;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;

class ItemDeductionController extends Controller
{
  public function create(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    $invoiceItem->load('invoiceable');
    $data['invoiceItem'] = $invoiceItem;
    $data['deduction_rates'] = InvoiceConfig::whereIn('config_type', ['Down Payment'])->activeOnly()->get();
    $data['invoice'] = $invoice;
    if ($invoiceItem->invoiceable_type == 'App\Models\CustomInvoiceItem') {
      request()->merge(['item' => 'custom']);
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function store(Invoice $invoice, InvoiceItem $invoiceItem, ItemDeductionStoreRequest $request)
  {
    $invoiceItem->deduction()->delete();
    $invoiceItem->deduction()->create([
      'deductible_id' => $invoiceItem->id,
      'deductible_type' => InvoiceItem::class,
      'downpayment_id' => $request->downpayment_id,
      'dp_rate_id' => $request->dp_rate_id,
      'is_percentage' => $request->deduction_rate->type != 'Fixed',
      'amount' => $request->calculated_downpayment_amount,
      'manual_amount' => $request->manual_deduction_amount,
      'percentage' => $request->downpayment_rate->amount ?? 0,
      'is_before_tax' => $request->is_before_tax,
      'calculation_source' => $request->calculation_source,
    ]);

    $invoiceItem->reCalculateTotal();
    $invoice->reCalculateTotal();

    return $this->sendRes('Deduction Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, InvoiceItem $invoiceItem, $deduction)
  {
    $invoiceItem->deduction()->delete();

    $invoiceItem->reCalculateTotal();
    $invoice->reCalculateTotal();

    return $this->sendRes('Deduction Removed Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }
}
