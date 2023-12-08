<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Models\ContractPhase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TotalAmountAdjustmentController extends Controller
{
  public function create(ContractPhase $phase)
  {
    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.total-amount-adjustments.create', compact('phase'))->render()]);
  }

  public function store(ContractPhase $phase, Request $request)
  {
    $request->validate([
      'adjuted_total_amount' => ['required', 'numeric'],
    ]);

    $phase_total = cMoney($phase->getRawOriginal('total_cost') / 1000, $phase->contract->currency)->getAmount();

    // adjusted amount should be +- 0.5 difference from total amount
    if (abs($request->adjuted_total_amount - $phase_total) > 0.5) {
      throw ValidationException::withMessages(['adjuted_total_amount' => 'Adjusted amount should be between ' . ($phase_total - 0.5) . ' and ' . ($phase_total + 0.5) . '.']);
    }

    DB::beginTransaction();

    try {
      $phase->update([
        'total_amount_adjustment' => moneyToInt($request->adjuted_total_amount - $phase_total),
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
