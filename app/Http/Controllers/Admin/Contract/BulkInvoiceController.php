<?php

namespace App\Http\Controllers\Admin\Contract;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Http\Request;

class BulkInvoiceController extends Controller
{
  public function store(Contract $contract, Request $request)
  {
    $request->validate([
      'phases' => 'required|array',
      'phases.*' => 'required|exists:contract_phases,id,contract_id,' . $contract->id
    ]);

    $phases = $contract->phases()->whereIn('id', $request->phases)->has('addedAsInvoiceItem', 0)->get();

    foreach($phases as $phase)
    {
      $invoice = Invoice::create([
        'company_id' => $contract->assignable_id,
        'contract_id' => $contract->id,
        'invoice_date' => now(),
        'due_date' => $phase->due_date,
        'type' => 'Regular',
        'is_auto_generated' => 1,
      ]);

      $invoice->attachPhasesWithTax([$phase->id]);
    }

    return $this->sendRes('Invoice Added Successfully');
  }
}
