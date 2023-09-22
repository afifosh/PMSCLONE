<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;

class InvoiceTaxController extends Controller
{
  public function store(Invoice $invoice, Request $request)
  {
    $request->validate([
      'taxes' => 'nullable|array',
      'taxes.*' => 'nullable|exists:taxes,id',
    ]);

    $taxes = filterInputIds($request->taxes ?? []);
    $rates = Tax::whereIn('id', $taxes)->get();

    $sync_data = [];
    foreach ($rates as $rate) {
      $sync_data[$rate->id] = ['amount' => $rate->amount, 'type' => $rate->type, 'invoice_item_id' => $request->item_id, 'invoice_id' => $invoice->id];
    }

    if ($request->item_id) {
      $item = $invoice->items()->where('id', $request->item_id)->with('invoiceable')->first();
      $item->taxes()->sync($sync_data);
      $item->updateTaxAmount();
    } else
      $invoice->taxes()->sync($sync_data);
      $invoice->updateTaxAmount();

    return $this->sendRes('success');
  }
}
