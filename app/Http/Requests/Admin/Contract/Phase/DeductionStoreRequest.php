<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeductionStoreRequest extends FormRequest
{
  /**
   * The tax rates for the phase. will be used in validation and controller as well
   */
  public $tax_rates = [];

  /**
   * calculated tax amount for the phase
   */
  public $calculated_tax_amount = 0;

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
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation(): void
  {
    $this->merge([
      'is_fixed_amount' => $this->boolean('is_fixed_amount'),
      'is_before_tax' => $this->boolean('is_before_tax'),
      'is_manual_deduction' => $this->boolean('is_fixed_amount') ? false : $this->boolean('is_manual_deduction')
    ]);

    $this->validate($this->getValidationRules(), $this->messages());
    if(!$this->is_fixed_amount)
      $this->deduction_rate = InvoiceConfig::activeOnly()->findOrFail($this->dp_rate_id);

    $this->downpayment = Invoice::findOrFail($this->downpayment_id);

    $this->phase->load('taxes');
    $this->tax_rates = $this->phase->taxes;
    // $fixed_tax = $this->taxes->where('type', 'Fixed')->sum('amount');
    // $percent_tax = $this->taxes->where('type', 'Percent')->sum('amount');
    // $this->calculated_tax_amount = $fixed_tax + ($percent_tax * $this->phase->estimated_cost / 100);

    $this->calDeductionAmount();

    $this->merge([
      'manual_deduction_amount' => ($this->is_manual_deduction && $this->downpayment_amount != $this->calculated_downpayment_amount) ? $this->downpayment_amount : 0,
    ]);
  }

  private function getValidationRules()
  {
    return [
      'is_before_tax' => 'required|boolean',
      'is_fixed_amount' => 'required|boolean',
      'downpayment_id' => 'required|exists:invoices,id',
      'downpayment_amount' => 'nullable|numeric|gte:0',
      'dp_rate_id' => ['nullable', Rule::requiredIf($this->is_fixed_amount == false), 'exists:invoice_configs,id,config_type,Down Payment'],
      'calculation_source' => 'required|in:Down Payment,Deductible',
      'is_manual_deduction' => 'required|boolean',
    ];
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $rules = [
      'downpayment_amount' => 'nullable|numeric|gte:0|max:' . $this->getMaxDeductableAmount(),
      'manual_deduction_amount' => 'required|numeric|gte:0',
    ] + $this->getValidationRules();

    if ($this->is_manual_deduction) {
      // manual deduction should be between +-1 of calculated deduction amount
      $rules['downpayment_amount'] = 'required|numeric|between:' . ($this->calculated_downpayment_amount - 1) . ',' . ($this->calculated_downpayment_amount + 1);
    }

    return $rules;
  }

  public function messages()
  {
    return [];
  }

  private function getMaxDeductableAmount(){
    return $this->downpayment->downpaymentAmountRemaining();
  }

  private function calTaxAmount(): void
  {
    $this->total_tax_amount = 0;

    foreach ($this->tax_rates as $index => $rate) {

      // calculate tax amount for each tax and validate it with difference of 1
      $tax_amount = $rate->amount * $this->phase->estimated_cost / 100;
      // if (abs($this->taxes[$index]['total_tax'] - $tax_amount) > 1) {
      //   $this->errors()->add('taxes.' . $index . '.total_tax', 'Tax amount should be between ' . ($tax_amount - 1) . ' and ' . ($tax_amount + 1));
      // }

      if($rate->category == 3){
        continue;
      }

      if ($rate->category == 2)
        $this->total_tax_amount -= $tax_amount;
      else {
        $this->total_tax_amount += $tax_amount;
      }
    }
  }

  private function calDeductionAmount(): void
  {
    $this->calculated_downpayment_amount = 0;

    if ($this->is_fixed_amount) {
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
        $this->calculated_downpayment_amount = ($this->phase->estimated_cost * $this->deduction_rate->amount) / 100;
      } else {
        $this->calTaxAmount();
        $this->calculated_downpayment_amount = (($this->phase->estimated_cost + ($this->total_tax_amount)) * $this->deduction_rate->amount) / 100;
      }
    }
  }
}
