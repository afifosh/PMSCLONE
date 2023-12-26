<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Models\ContractPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubtotalAdjustmentController extends Controller
{
  public function __construct()
  {
    // check if contractPhase belongs to contract
    $this->middleware('verifyContractNotTempered:phase,contract_id')->only(['create', 'store']);

    $this->middleware('permission:create contract|update contract')->only(['create', 'store']);
  }

  public function create($contract, ContractPhase $phase)
  {
    if ($phase->isPaid()) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.subtotal-adjustments.create', compact('phase'))->render()]);
  }

  public function store($contract, ContractPhase $phase, Request $request)
  {
    if ($phase->isPaid()) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }

    $request->validate([
      'adjuted_subtotal_amount' => ['required', 'numeric'],
    ]);

    $phase_subtotal = cMoney($phase->subtotal_row_raw, $phase->contract->currency)->getAmount();

    // adjusted amount should be +- 0.5 difference from total amount
    if (abs($request->adjuted_subtotal_amount - $phase_subtotal) > 0.5) {
      throw ValidationException::withMessages(['adjuted_subtotal_amount' => 'Adjusted amount should be between ' . ($phase_subtotal - 0.5) . ' and ' . ($phase_subtotal + 0.5) . '.']);
    }

    DB::beginTransaction();

    try {
      $phase->update([
        'subtotal_amount_adjustment' => ($request->adjuted_subtotal_amount - $phase_subtotal),
      ]);

      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Subtotal amount adjustment added successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendErr($e->getMessage());
    }
  }
}
