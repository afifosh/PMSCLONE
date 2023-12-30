<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Models\ContractPhase;
use App\Models\PhaseCostAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhaseCostAdjustmentController extends Controller
{
  public function __construct()
  {
    // check if phase belongs to contract
    $this->middleware('verifyContractNotTempered:phase,contract_id')->only(['create', 'store', 'edit', 'update', 'destroy']);

    // check if tax belongs to phase
    // $this->middleware('verifyContractNotTempered:costAdjustment,phase_id,phase,id')->only(['edit', 'update', 'destroy']);

    $this->middleware('permission:create contract')->only(['create', 'store']);
    $this->middleware('permission:update contract')->only(['edit', 'update']);
    $this->middleware('permission:delete contract')->only(['destroy']);
  }

  public function create($contract, ContractPhase $phase)
  {
    $costAdjustment = new PhaseCostAdjustment();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.cost-adjustments.create', compact('phase', 'costAdjustment'))->render()]);
  }

  public function store($contract, ContractPhase $phase, Request $request)
  {
    abort_if(!$phase->is_allowable_cost || !$phase->isPartialPaid(), 403, 'You can not edit this phase because it is not allowable cost or Paid');

    $request->validate([
      'amount' => 'required|numeric|min:0',
      'description' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
      $phase->costAdjustments()->create([
        'amount' => $request->amount,
        'description' => $request->description,
      ]);

      $phase->reCalculateCost();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();
      return $this->sendRes(__('Adjustment Created Successfully'), ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function edit($contract, ContractPhase $phase, PhaseCostAdjustment $costAdjustment)
  {
    abort_if(!$phase->is_allowable_cost || !$phase->isPartialPaid(), 403, 'You can not edit this phase because it is not allowable cost or Paid');

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.cost-adjustments.create', compact('phase', 'costAdjustment'))->render()]);
  }

  public function update($contract, ContractPhase $phase, PhaseCostAdjustment $costAdjustment, Request $request)
  {
    abort_if(!$phase->is_allowable_cost || !$phase->isPartialPaid(), 403, 'You can not edit this phase because it is not allowable cost or Paid');

    $request->validate([
      'amount' => 'required|numeric|min:0',
      'description' => 'required|string',
    ]);

    DB::beginTransaction();
    try {
      $costAdjustment->update([
        'amount' => $request->amount,
        'description' => $request->description,
      ]);

      $phase->reCalculateCost();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes(__('Adjustment Updated Successfully'), ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function destroy($contract, ContractPhase $phase, PhaseCostAdjustment $costAdjustment)
  {
    abort_if(!$phase->is_allowable_cost || !$phase->isPartialPaid(), 403, 'You can not edit this phase because it is paid or not allowable cost or Paid');

    DB::beginTransaction();
    try {
      $costAdjustment->delete();

      $phase->reCalculateCost();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();
      return $this->sendRes(__('Adjustment Deleted Successfully'), ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }
}
