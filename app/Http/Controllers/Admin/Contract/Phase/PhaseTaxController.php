<?php

namespace App\Http\Controllers\Admin\Contract\Phase;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Contract\Phase\TaxStoreRequest;
use App\Models\ContractPhase;
use App\Models\InvoiceConfig;
use App\Models\PhaseTax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhaseTaxController extends Controller
{
  public function create($contract, ContractPhase $phase)
  {
    $data['phase'] = $phase;
    $data['tax_rates'] = InvoiceConfig::activeTaxes()->get();
    $data['tax'] = new PhaseTax();
    $data['stages'] = [$phase->stage->name];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.taxes.create', $data)->render()]);
  }

  public function store($contract, ContractPhase $phase, TaxStoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $phase->taxes()->attach($request->tax_rate->id, [
        'amount' => $request->tax_rate->getRawOriginal('amount'),
        'type' => $request->tax_rate->type,
        'calculated_amount' => moneyToInt($request->calculated_tax_amount),
        'manual_amount' => $request->is_manual_tax ? moneyToInt($request->total_tax) : 0,
        'category' => $request->tax_rate->category,
      ]);

      if ($phase->deduction && !$phase->deduction->is_before_tax) {
        $phase->recalculateDeductionAmount();
      }

      $phase->reCalculateTotal();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Tax added successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id]), 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function edit($contract, ContractPhase $phase, PhaseTax $tax)
  {
    $data['phase'] = $phase;
    $data['tax_rates'] = InvoiceConfig::activeTaxes()->get();
    $data['tax'] = $tax;
    $data['stages'] = [$phase->stage->name];

    return $this->sendRes('success', ['view_data' => view('admin.pages.contracts.phases.taxes.create', $data)->render()]);
  }

  public function update($contract, ContractPhase $phase, PhaseTax $tax, TaxStoreRequest $request)
  {
    DB::beginTransaction();
    try {
      $tax->delete();
      $phase->taxes()->attach($request->tax_rate->id, [
        'amount' => $request->tax_rate->getRawOriginal('amount'),
        'type' => $request->tax_rate->type,
        'calculated_amount' => moneyToInt($request->calculated_tax_amount),
        'manual_amount' => $request->is_manual_tax ? moneyToInt($request->total_tax) : 0,
        'category' => $request->tax_rate->category,
      ]);

      if ($phase->deduction && !$phase->deduction->is_before_tax) {
        $phase->recalculateDeductionAmount();
      }

      $phase->reCalculateTotal();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Tax Updated successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id]), 'close' => 'globalModal']);
    } catch (\Exception $e) {
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }

  public function destroy($contract, ContractPhase $phase, PhaseTax $tax)
  {
    DB::beginTransaction();
    try {
      $tax->delete();

      if ($phase->deduction && !$phase->deduction->is_before_tax) {
        $phase->recalculateDeductionAmount();
      }

      $phase->reCalculateTotal();

      // if added in invoice then update invoice item and tax amount
      $phase->syncUpdateWithInvoices();

      DB::commit();

      return $this->sendRes('Tax deleted successfully.', ['event' => 'functionCall', 'function' => 'reloadTableAndActivePhase', 'function_params' => json_encode(['phase_id' => $phase->id])]);
    } catch (\Exception $e) {
      return $this->sendError($e->getMessage());
    }
  }
}