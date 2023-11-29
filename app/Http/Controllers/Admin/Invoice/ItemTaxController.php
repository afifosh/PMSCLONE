<?php

namespace App\Http\Controllers\Admin\Invoice;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Invoice\ItemTaxStoreRequest;
use App\Models\ContractPhase;
use App\Models\Invoice;
use App\Models\InvoiceConfig;
use App\Models\InvoiceItem;
use App\Models\InvoiceTax;
use Illuminate\Support\Facades\DB;

class ItemTaxController extends Controller
{
  public function create(Invoice $invoice, InvoiceItem $invoiceItem)
  {
    $invoiceItem->load('invoiceable');
    $data['invoiceItem'] = $invoiceItem;
    $data['tax_rates'] = InvoiceConfig::whereIn('config_type', ['Tax', 'Down Payment'])->activeOnly()->get();
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

  public function store(Invoice $invoice, InvoiceItem $invoiceItem, ItemTaxStoreRequest $request)
  {
    DB::beginTransaction();
    try{
      $invoiceItem->taxes()->attach($request->tax->id, [
        'calculated_amount' => moneyToInt($request->calculated_tax_amount),
        'manual_amount' => moneyToInt($request->manual_tax_amount),
        'pay_on_behalf' => $request->pay_on_behalf,
        'is_authority_tax' => $request->pay_on_behalf ? true : $request->is_authority_tax,
        'amount' => $request->tax->getRawOriginal('amount'),
        'type' => $request->tax->type,
        'is_simple_tax' => $request->tab == 'summary',
        'invoice_item_id' => $invoiceItem->id,
        'invoice_id' => $invoiceItem->invoice_id
      ]);

      if ($invoiceItem->deduction && !$invoiceItem->deduction->is_before_tax) {
        $invoiceItem->reCalculateTotal();
        $invoiceItem->recalculateDeductionAmount();
      }

      $invoiceItem->reCalculateTotal();
      $invoice->reCalculateTotal();
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Something went wrong');
    }
    DB::commit();

    return $this->sendRes('Tax Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function edit(Invoice $invoice, InvoiceItem $invoiceItem, InvoiceTax $tax)
  {
    $invoiceItem->load('invoiceable');
    $data['invoiceItem'] = $invoiceItem;
    $data['tax_rates'] = InvoiceConfig::whereIn('config_type', ['Tax', 'Down Payment'])->activeOnly()->get();
    $data['invoice'] = $invoice;
    if($invoiceItem->invoiceable_type == ContractPhase::class){
      $invoiceItem->load('invoiceable.stage');
      $data['phases'] = [$invoiceItem->invoiceable_id => $invoiceItem->invoiceable->name];
      $data['stages'] = [$invoiceItem->invoiceable->stage_id => $invoiceItem->invoiceable->stage->name];
    }else{
      request()->merge(['item' => 'custom']);
    }

    $data['pivot_tax'] = $tax;

    return $this->sendRes('success', ['view_data' => view('admin.pages.invoices.items.edit', $data)->render()]);
  }

  public function update(Invoice $invoice, InvoiceItem $invoiceItem, InvoiceTax $tax, ItemTaxStoreRequest $request)
  {
    DB::beginTransaction();
    try{
      $tax->update([
        'tax_id' => $request->tax->id,
        'calculated_amount' => $request->calculated_tax_amount,
        'manual_amount' => $request->manual_tax_amount,
        'pay_on_behalf' => $request->pay_on_behalf,
        'is_authority_tax' => $request->pay_on_behalf ? true : $request->is_authority_tax,
        'amount' => $request->tax->amount,
        'type' => $request->tax->type
      ]);

      if ($invoiceItem->deduction && !$invoiceItem->deduction->is_before_tax) {
        $invoiceItem->reCalculateTotal();
        $invoiceItem->recalculateDeductionAmount();
      }

      $invoiceItem->reCalculateTotal();
      $invoice->reCalculateTotal();
    } catch (\Exception $e) {
      DB::rollBack();
      return $this->sendError('Something went wrong');
    }
    DB::commit();

    return $this->sendRes('Tax Added Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }

  public function destroy(Invoice $invoice, InvoiceItem $invoiceItem, $tax)
  {
    InvoiceTax::where('id', $tax)->delete();

    if ($invoiceItem->deduction && !$invoiceItem->deduction->is_before_tax) {
      $invoiceItem->reCalculateTotal();
      $invoiceItem->recalculateDeductionAmount();
    }
    $invoiceItem->reCalculateTotal();
    $invoice->reCalculateTotal();

    return $this->sendRes('Tax Removed Successfully', ['event' => 'functionCall', 'function' => 'reloadPhasesList', 'close' => 'globalModal']);
  }
}
