<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TotalAmountAdjustmentController extends Controller
{
  public function __construct()
  {
    // check if phase belongs to contract
    $this->middleware('verifyContractNotTempered:phase,contract_id')->only(['create', 'store']);

    $this->middleware('permission:create contract|update contract')->only(['create', 'store']);
  }

  public function create($contract, ContractPhase $phase)
  {
    abort_if(!$phase->taxes()->exists() || !$phase->deduction, 403, 'Phase does not have taxes or deduction.');

    if ($phase->isPaid()) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.total-amount-adjustments.create', compact('phase'))->render()]);
  }

  public function store($contract, ContractPhase $phase, Request $request)
  {
    abort_if(!$phase->taxes()->exists() || !$phase->deduction, 403, 'Phase does not have taxes or deduction.');

    if ($phase->isPaid()) {
      return $this->sendError('You can not edit this phase because it is in paid invoice');
    }

    $request->validate([
      'adjuted_total_amount' => ['required', 'numeric'],
    ]);

    $phase_total = cMoney($phase->getRawOriginal('total_cost') / 100, $phase->contract->currency)->getAmount();

    // adjusted amount should be +- 0.5 difference from total amount
    if (abs($request->adjuted_total_amount - $phase_total) > 0.5) {
      throw ValidationException::withMessages(['adjuted_total_amount' => 'Adjusted amount should be between ' . ($phase_total - 0.5) . ' and ' . ($phase_total + 0.5) . '.']);
    }

    DB::beginTransaction();

    try {
      $phase->update([
        'total_amount_adjustment' => $request->adjuted_total_amount - $phase_total,
      ]);

      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Total amount adjustment added successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendErr($e->getMessage());
    }
  }
}
