<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Http\Controllers\Controller;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubtotalAdjustmentController extends Controller
{
  public function create(InvoiceItem $invoiceItem)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit.subtotal-adjustment-form', compact('invoiceItem'))->render()]);
  }

  public function store(InvoiceItem $invoiceItem, Request $request)
  {
    $request->validate([
      'adjuted_subtotal_amount' => ['required', 'numeric'],
    ]);

    $item_subtotal = cMoney($invoiceItem->subtotal_row_raw, $invoiceItem->invoice->contract->currency)->getAmount();

    // adjusted amount should be +- 0.5 difference from total amount
    if (abs($request->adjuted_subtotal_amount - $item_subtotal) > 0.5) {
      throw ValidationException::withMessages(['adjuted_subtotal_amount' => 'Adjusted amount should be between ' . ($item_subtotal - 0.5) . ' and ' . ($item_subtotal + 0.5) . '.']);
    }

    DB::beginTransaction();

    try {
      $invoiceItem->update([
        'subtotal_amount_adjustment' => $request->adjuted_subtotal_amount - $item_subtotal,
      ]);

      $invoiceItem->syncUpdateWithPhase();

      DB::commit();

      return $this->sendRes('Adjustment Added successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendErr($e->getMessage());
    }
  }
}
