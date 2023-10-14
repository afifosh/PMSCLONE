<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DownpaymentController extends Controller
{
  public function store(Invoice $invoice, Request $request)
  {
    $request->validate([
      'downpayment_id' => 'required|exists:invoices,id',
      'downpayment_type' => 'required|in:Fixed,Percentage',
      'downpayment_value' => 'required|numeric|gte:0',
      'deduction_type' => 'required|in:before_tax,after_tax'
    ]);

    $downpayment = Invoice::find($request->downpayment_id);

    $maxAmount = $downpayment->downpaymentAmountRemaining();
    $amount = $request->downpayment_type == 'Fixed' ? moneyToInt($request->downpayment_value) : (moneyToInt($request->downpayment_value * $downpayment->total) / 100);

    if($amount > moneyToInt($maxAmount)){
      if($request->downpayment_type == 'Fixed')
        throw ValidationException::withMessages(['downpayment_value' => 'Available amount is' . $maxAmount]);
      else
        throw ValidationException::withMessages(['downpayment_amount' => 'Available amount is' . $maxAmount]);
    }

    if ($request->downpayment_value == 0) {
      $invoice->downPayments()->detach($downpayment->id);
    } else {
      $invoice->downPayments()->syncWithoutDetaching([$downpayment->id => [
        'is_percentage' => $request->downpayment_type == 'Percentage',
        'percentage' => $request->downpayment_type == 'Percentage' ? moneyToInt($request->downpayment_value) : 0,
        'amount' => $amount,
        'is_after_tax' => $request->deduction_type == 'after_tax'
      ]]);
    }

    $invoice->reCalculateTotal();

    return $this->sendRes('Downpayment added successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList']);
  }
}
