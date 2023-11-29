<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\ItemDeductionStoreRequest;
use App\Models\ContractPhase;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class ItemDeductionController extends Controller
{
  public function create(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    $invoiceItem->load('invoiceable');
    $data['invoiceItem'] = $invoiceItem;
    $data['deduction_rates'] = InvoiceConfig::whereIn('config_type', ['Down Payment'])->activeOnly()->get();
    $data['invoice'] = $invoice;
    if($invoiceItem->invoiceable_type == ContractPhase::class){
      $invoiceItem->load('invoiceable.stage');
      $data['phases'] = [$invoiceItem->invoiceable_id => $invoiceItem->invoiceable->name];
      $data['stages'] = [$invoiceItem->invoiceable->stage_id => $invoiceItem->invoiceable->stage->name];
      request()->merge(['item' => 'phase']);
    }else{
      request()->merge(['item' => 'custom']);
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function store(Invoice $invoice, InvoiceItem $invoiceItem, ItemDeductionStoreRequest $request)
  {
    if($invoiceItem->deduction()->exists())
      return $this->sendError('Deduction Already Exists');

    DB::beginTransaction();
    try{
      $invoiceItem->deduction()->create([
        'deductible_id' => $invoiceItem->id,
        'deductible_type' => InvoiceItem::class,
        'downpayment_id' => $request->downpayment_id,
        'dp_rate_id' => $request->dp_rate_id,
        'is_percentage' => $request->is_fixed_amount ? false : ($request->deduction_rate->type != 'Fixed'),
        'amount' => $request->is_fixed_amount ? $request->downpayment_amount : $request->calculated_downpayment_amount,
        'manual_amount' => $request->manual_deduction_amount,
        'percentage' => $request->deduction_rate->amount ?? 0,
        'is_before_tax' => $request->is_before_tax,
        'calculation_source' => $request->calculation_source,
      ]);

      if($request->is_before_tax)
        $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts();
      $invoiceItem->reCalculateTotal();
      $invoice->reCalculateTotal();
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
    DB::commit();

    return $this->sendRes('Deduction Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function edit(Invoice $invoice, InvoiceItem $invoiceItem, $deduction)
  {
    $invoiceItem->load('invoiceable', 'deduction');
    $data['invoiceItem'] = $invoiceItem;
    $data['deduction_rates'] = InvoiceConfig::whereIn('config_type', ['Down Payment'])->activeOnly()->get();
    $data['added_deduction'] = $invoiceItem->deduction;
    $data['invoice'] = $invoice;
    if($invoiceItem->invoiceable_type == ContractPhase::class){
      $invoiceItem->load('invoiceable.stage');
      $data['phases'] = [$invoiceItem->invoiceable_id => $invoiceItem->invoiceable->name];
      $data['stages'] = [$invoiceItem->invoiceable->stage_id => $invoiceItem->invoiceable->stage->name];
      request()->merge(['item' => 'phase']);
    }else{
      request()->merge(['item' => 'custom']);
    }

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function update(Invoice $invoice, InvoiceItem $invoiceItem, $deduction, ItemDeductionStoreRequest $request)
  {
    if(!$invoiceItem->deduction()->exists())
      return $this->sendError('Deduction Not Found');
    DB::beginTransaction();
    try{
      $oldDeduction = $invoiceItem->deduction;
      if(!$request->is_before_tax){
        $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts(false); // false to not calculate deduction amount in tax calculation
        $invoiceItem->reCalculateTotal();
        $invoiceItem->refresh();
      }
      $invoiceItem->deduction()->update([
        'downpayment_id' => $request->downpayment_id,
        'dp_rate_id' => $request->dp_rate_id,
        'is_percentage' => $request->is_fixed_amount ? false : ($request->deduction_rate->type != 'Fixed'),
        'amount' => moneyToInt($request->is_fixed_amount ? $request->downpayment_amount : $request->calculated_downpayment_amount),
        'manual_amount' => $request->manual_deduction_amount,
        'percentage' => $request->deduction_rate->amount ?? 0,
        'is_before_tax' => $request->is_before_tax,
        'calculation_source' => $request->calculation_source,
      ]);
      $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts();
      $invoiceItem->reCalculateDeductionAmount();
      $invoiceItem->reCalculateTotal();
      $invoice->reCalculateTotal();
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
    DB::commit();

    return $this->sendRes('Deduction Updated Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, InvoiceItem $invoiceItem, $deduction)
  {
    DB::beginTransaction();
    try{
      $is_before_tax = $invoiceItem->deduction->is_before_tax;
      $invoiceItem->deduction()->delete();
      if($is_before_tax)
        $invoiceItem->reCalculateTaxAmountsAndResetManualAmounts(false);
      $invoiceItem->reCalculateTotal();
      $invoice->reCalculateTotal();
      DB::commit();

      return $this->sendRes('Deduction Removed Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
    }catch(\Exception $e){
      DB::rollback();
      return $this->sendError($e->getMessage());
    }
  }
}
