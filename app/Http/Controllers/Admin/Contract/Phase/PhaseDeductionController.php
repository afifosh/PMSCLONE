<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Phase\DeductionStoreRequest;
use App\Models\Contract;
use App\Models\ContractPhase;
use App\Models\InvoiceConfig;
use App\Models\InvoiceDeduction;
use App\Models\PhaseTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhaseDeductionController extends Controller
{
  public function create(Contract $contract, ContractPhase $phase)
  {
    $contract->load('deductableDownpayments');
    $data['contract'] = $contract;
    $data['phase'] = $phase;
    $data['deduction'] = new InvoiceDeduction();
    $data['stages'] = [$phase->stage->name];
    $data['tax_rates'] = InvoiceConfig::where('config_type', 'Down Payment')->activeOnly()->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.deductions.create', $data)->render()]);
  }

  public function store($contract, ContractPhase $phase, DeductionStoreRequest $request)
  {
    if ($phase->deduction()->exists())
      return $this->sendError('Deduction Already Exists');

    DB::beginTransaction();
    try {
      $phase->deduction()->create([
        'deductible_id' => $phase->id,
        'deductible_type' => ContractPhase::class,
        'downpayment_id' => $request->downpayment_id,
        'dp_rate_id' => $request->dp_rate_id,
        'is_percentage' => $request->is_fixed_amount ? false : ($request->deduction_rate->type != 'Fixed'),
        'amount' => $request->is_fixed_amount ? $request->downpayment_amount : $request->calculated_downpayment_amount,
        'manual_amount' => $request->manual_deduction_amount,
        'percentage' => $request->deduction_rate->amount ?? 0,
        'is_before_tax' => $request->is_before_tax,
        'calculation_source' => $request->calculation_source,
      ]);

      if ($request->is_before_tax) {
        $phase->reCalculateTotal();
        $phase->reCalculateTaxAmountsAndResetManualAmounts();
      }

      $phase->reCalculateTotal();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Deduction added successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function edit(Contract $contract, ContractPhase $phase, InvoiceDeduction $deduction)
  {
    $contract->load('deductableDownpayments');
    $data['contract'] = $contract;
    $data['phase'] = $phase;
    $data['deduction'] = $deduction;
    $data['stages'] = [$phase->stage->name];
    $data['tax_rates'] = InvoiceConfig::where('config_type', 'Down Payment')->activeOnly()->get();

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.deductions.create', $data)->render()]);
  }

  public function update($contract, ContractPhase $phase, InvoiceDeduction $deduction, DeductionStoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $old_is_before_tax = $phase->deduction->is_before_tax;
      $phase->deduction()->update([
        'downpayment_id' => $request->downpayment_id,
        'dp_rate_id' => $request->dp_rate_id,
        'is_percentage' => $request->is_fixed_amount ? false : ($request->deduction_rate->type != 'Fixed'),
        'amount' => moneyToInt($request->is_fixed_amount ? $request->downpayment_amount : $request->calculated_downpayment_amount),
        'manual_amount' => moneyToInt($request->manual_deduction_amount),
        'percentage' => $request->deduction_rate->amount ?? 0,
        'is_before_tax' => $request->is_before_tax,
        'calculation_source' => $request->calculation_source,
      ]);
      $phase->reCalculateTotal();
      if($old_is_before_tax || $request->is_before_tax){
        $phase->reCalculateTaxAmountsAndResetManualAmounts();
        $phase->reCalculateTotal();
      }

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Deduction updated successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function destroy($contract, ContractPhase $phase, InvoiceDeduction $deduction)
  {
    DB::beginTransaction();
    try {
      $is_before_tax = $phase->deduction->is_before_tax;
      $phase->deduction()->delete();
      if ($is_before_tax)
        $phase->reCalculateTaxAmountsAndResetManualAmounts(false);
      $phase->reCalculateTotal();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Deduction removed successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }
}
