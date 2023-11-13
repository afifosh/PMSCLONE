<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Http\Request;

class InvoiceTaxController extends Controller
{
  public function store(Invoice $invoice, Request $request)
  {
    $request->validate([
      'taxes' => 'nullable|array',
      'taxes.*' => 'nullable|exists:invoice_configs,id',
    ]);

    $taxes = filterInputIds($request->taxes ?? []);
    $rates = InvoiceConfig::whereIn('id', $taxes)->get();

    $sync_data = [];
    foreach ($rates as $rate) {
      $sync_data[$rate->id] = ['amount' => $rate->getRawOriginal('amount'), 'type' => $rate->type, 'invoice_item_id' => $request->item_id, 'invoice_id' => $invoice->id];
    }

    $invoice->taxes()->sync($sync_data);
    $invoice->reCalculateTotal();

    return $this->sendRes('success');
  }
}
