<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;

class ItemDeductionStoreRequest extends FormRequest
{
  /**
   * Total Tax Amount
   */
  public $total_tax_amount = 0;

  /**
   * Downpayment
   */
  public $downpayment;

  /**
   * Deduction Rate
   */
  public $deduction_rate;

  /**
   * Deduction Amount
   */
  public $calculated_downpayment_amount = 0;

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation(): void
  {
    $this->merge([
      'subtotal' => $this->invoice_item->subtotal,
      /** Deduction Related Fields */
      'is_before_tax' => $this->boolean('is_before_tax'),
      'is_manual_deduction' => $this->boolean('is_fixed_amount') ? false : $this->boolean('is_manual_deduction'),
      'is_fixed_amount' => $this->boolean('is_fixed_amount'),
      'downpayment_amount' => $this->downpayment_amount,
    ]);

    // validate
    $this->validate($this->getItemRules(), $this->messaages());
    if(!$this->is_fixed_amount)
    $this->deduction_rate = InvoiceConfig::activeOnly()->findOrFail($this->dp_rate_id);

    $this->downpayment = Invoice::findOrFail($this->downpayment_id);

    $this->calTaxAmount();
    $this->calDeductionAmount();

    $this->merge([
      'manual_deduction_amount' => ($this->is_manual_deduction && $this->downpayment_amount != $this->calculated_downpayment_amount) ? $this->downpayment_amount : 0,
    ]);

    $this->merge([
      // get the value after floating point (decimal) of total
      'rounding_amount' => $this->rounding_amount ? (floor($this->total) - $this->total) : 0,
    ]);
  }
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  private function getItemRules(): array
  {
    return [
      'subtotal' => 'required|numeric|gt:0',
      // deduction fields
      'is_before_tax' => 'required|boolean',
      'is_fixed_amount' => 'required|boolean',
      'downpayment_id' => 'required|exists:invoices,id',
      'downpayment_amount' => 'required|numeric|gt:0',
      'dp_rate_id' => 'nullable|required_if:is_fixed_amount,false|exists:invoice_configs,id',
      'calculation_source' => 'required|in:Down Payment,Deductible',
      'is_manual_deduction' => 'required|boolean',
    ];
  }
  public function rules(): array
  {
    $rules = [
      'downpayment_amount' => 'required|numeric|gt:0|max:' . $this->getMaxDeductableAmount(),
      'manual_deduction_amount' => 'required|numeric|gte:0',
    ] + $this->getItemRules();

    if ($this->is_manual_deduction) {
      // manual deduction should be between +-1 of calculated deduction amount
      $rules['downpayment_amount'] = 'required|numeric|between:' . ($this->calculated_downpayment_amount - 1) . ',' . ($this->calculated_downpayment_amount + 1);
    }

    return $rules;
  }

  private function getMaxDeductableAmount(){
    $maxDeductable = $this->downpayment->downpaymentAmountRemaining();
    if($this->method() == 'POST'){
      return $maxDeductable;
    }else{
      if(@$this->invoice_item->deduction){
        $maxDeductable += ($this->invoice_item->deduction->manual_amount ? $this->invoice_item->deduction->manual_amount : $this->invoice_item->deduction->amount);
      }
    }

    return $maxDeductable;
  }

  private function calDeductionAmount(): void
  {
    $this->calculated_downpayment_amount = 0;

    if($this->is_fixed_amount){
      $this->calculated_downpayment_amount = $this->downpayment_amount;
      return;
    }

    if ($this->deduction_rate->type == 'Fixed') {
      $this->calculated_downpayment_amount = $this->deduction_rate->amount;
      return;
    }
    if ($this->calculation_source == 'Down Payment') {
        $this->calculated_downpayment_amount = ($this->downpayment->total * $this->deduction_rate->amount) / 100;
    } else {
      if($this->is_before_tax) {
        $this->calculated_downpayment_amount = ($this->subtotal * $this->deduction_rate->amount) / 100;
      } else {
        $this->calculated_downpayment_amount = (($this->subtotal + ($this->total_tax_amount)) * $this->deduction_rate->amount) / 100;
      }
    }
  }

  private function calTaxAmount(): void
  {
    $this->total_tax_amount = 0;
    if($this->is_before_tax) {
      return ;
    }
    $this->invoice_item->load('taxes');
    foreach ($this->invoice_item->taxes as $tax) {
      if($tax->pivot->category == 3)
        continue;
      // if method post then no need to reset manual amount
      if($this->method() == 'POST'){
        $this->total_tax_amount += (((($tax->pivot->manual_amount ? $tax->pivot->manual_amount : $tax->pivot->calculated_amount) * ($tax->pivot->category == 2 ? -1 : 1)))/1000);
      }else if ($tax->pivot->type == 'Fixed') {
        $this->total_tax_amount += $tax->amount;
      } else {
        $this->total_tax_amount += ((($this->taxableAmount() * $tax->amount) / 100) * ($tax->pivot->category == 2 ? -1 : 1));
      }
    }
  }

  private function taxableAmount()
  {
    return $this->subtotal;
  }

  public function messaages ()
  {
    return [];
  }
}
