<?php

namespace App\Http\Controllers\Admin\Invoice\Item;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TotalAmountAdjustmentController extends Controller
{
  public function __construct()
  {
    // check if invoiceItem belongs to invoice
    $this->middleware('verifyContractNotTempered:invoiceItem,invoice_id,invoice,id')->only(['create', 'store']);
  }

  public function create(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit.total-amount-adjustment-form', compact('invoiceItem'))->render()]);
  }

  public function store(Invoice $invoice, InvoiceItem $invoiceItem, Request $request)
  {
    $request->validate([
      'adjuted_total_amount' => ['required', 'numeric'],
    ]);

    $item_total = cMoney($invoiceItem->getRawOriginal('total') / 100, $invoiceItem->invoice->contract->currency)->getAmount();

    // adjusted amount should be +- 0.5 difference from total amount
    if (abs($request->adjuted_total_amount - $item_total) > 0.5) {
      throw ValidationException::withMessages(['adjuted_total_amount' => 'Adjusted amount should be between ' . ($item_total - 0.5) . ' and ' . ($item_total + 0.5) . '.']);
    }

    DB::beginTransaction();

    try{
      $invoiceItem->update([
        'total_amount_adjustment' => $request->adjuted_total_amount - $item_total,
      ]);

      $invoiceItem->syncUpdateWithPhase();

      $invoiceItem->invoice->reCalculateTotal();

      DB::commit();

      return $this->sendRes('Adjustment Added successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendErr($e->getMessage());
    }
  }
}
