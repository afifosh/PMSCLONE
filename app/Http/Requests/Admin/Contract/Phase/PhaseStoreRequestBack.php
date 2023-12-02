<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhaseStoreRequestBack extends FormRequest
{
  /**
   * The tax rates for the phase. will be used in validation and controller as well
   */
  public $tax_rates = null;

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
      'taxes' => $this->taxes ? $this->taxes : [],
      'add_deduction' => $this->boolean('add_deduction'),
      'is_fixed_amount' => $this->boolean('is_fixed_amount'),
      'is_before_tax' => $this->boolean('is_before_tax'),
      'is_manual_deduction' => $this->boolean('is_fixed_amount') ? false : $this->boolean('is_manual_deduction'),
      'downpayment_amount' => $this->boolean('add_deduction') ? $this->downpayment_amount : 0,
    ]);

    $this->validate($this->getValidationRules(), $this->messages());
    if($this->add_deduction){
      if(!$this->is_fixed_amount)
        $this->deduction_rate = InvoiceConfig::activeOnly()->findOrFail($this->dp_rate_id);

      $this->downpayment = Invoice::findOrFail($this->downpayment_id);
    }

    $tax_ids = array_column($this->taxes, 'phase_tax');
    $this->tax_rates = InvoiceConfig::whereIn('id', filterInputIds($tax_ids))->activeTaxes()->get();
    // $fixed_tax = $this->taxes->where('type', 'Fixed')->sum('amount');
    // $percent_tax = $this->taxes->where('type', 'Percent')->sum('amount');
    // $this->calculated_tax_amount = $fixed_tax + ($percent_tax * $this->estimated_cost / 100);

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

  private function getValidationRules()
  {
    return [
      'stage_id' => 'required|exists:contract_stages,id,contract_id,' . $this->contract->id,
      'name' => 'required|string|max:255|unique:contract_phases,name,NULL,id,stage_id,' . $this->stage_id,
      'estimated_cost' => [
        'required',
        'numeric',
        'gt:0',
        //'max:' .  ($this->contract->remaining_amount - $this->calculated_tax_amount))
      ],
      'total_cost' => [
        'required',
        'numeric',
        'gt:0',
        //'max:' . ($this->contract->remaining_amount))
      ],
      'taxes' => 'nullable|array',
      'taxes.*' => 'nullable|array',
      'taxes.*.phase_tax' => 'required|exists:invoice_configs,id',
      'taxes.*.total_tax' => 'required|numeric',
      'taxes.*.is_manual_tax' => 'required|array',
      'taxes.*.is_manual_tax.*' => 'nullable|boolean',
      // deduction fields
      'add_deduction' => 'boolean',
      'is_before_tax' => 'required_if:add_deduction,true|boolean',
      'is_fixed_amount' => 'required_if:add_deduction,true|boolean',
      'downpayment_id' => 'nullable|required_if:add_deduction,true|exists:invoices,id',
      'downpayment_amount' => 'required_if:add_deduction,true|numeric|gte:0',
      'dp_rate_id' => ['nullable', Rule::requiredIf($this->add_deduction && $this->is_fixed_amount == false), 'exists:invoice_configs,id'],
      'calculation_source' => 'required_if:add_deduction,true|in:Down Payment,Deductible',
      'is_manual_deduction' => 'required_if:add_deduction,true|boolean',
      // end deduction fields
      'description' => 'nullable|string|max:2000',
      'start_date' => 'required|date' . (request()->due_date ? '|before_or_equal:due_date' : '') . '|after_or_equal:' . $this->contract->start_date,
      'due_date' => 'nullable|date|after:start_date|before_or_equal:' . $this->contract->end_date,
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
      'downpayment_amount' => 'required|numeric|gte:0|max:' . $this->getMaxDeductableAmount(),
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
    return [
      'estimated_cost.max' => '',
      'stage_id.exists' => 'Please select a valid stage for this contract.',
      'stage_id.required' => 'The stage field is required. Please select a stage.',
      'taxes.*.*' => 'This field is required.',
    ];
  }

  private function getMaxDeductableAmount(){
    if(!$this->add_deduction){
      return 0;
    }

    return $this->downpayment->downpaymentAmountRemaining();
  }

  private function calTaxAmount(): void
  {
    $this->total_tax_amount = 0;

    if (!$this->taxes) {
      return;
    }

    foreach ($this->taxes as $index => $tax) {
      $rate = $this->tax_rates->where('id', $tax['phase_tax'])->first();

      // calculate tax amount for each tax and validate it with difference of 1
      $tax_amount = $rate->amount * $this->getTaxableAmount() / 100;
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

  private function getTaxableAmount()
  {
    if ($this->is_before_tax) {
      $this->calDeductionAmount();
      return $this->estimated_cost - $this->calculated_downpayment_amount;
    }

    return $this->estimated_cost;
  }

  private function calDeductionAmount(): void
  {
    $this->calculated_downpayment_amount = 0;

    if (!$this->add_deduction) {
      return;
    }

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
        $this->calculated_downpayment_amount = ($this->estimated_cost * $this->deduction_rate->amount) / 100;
      } else {
        $this->calculated_downpayment_amount = (($this->estimated_cost + ($this->total_tax_amount)) * $this->deduction_rate->amount) / 100;
      }
    }
  }

  // // after validation hook
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      foreach ($this->taxes as $index => $tax) {
        $rate = $this->tax_rates->where('id', $tax['phase_tax'])->first();
        // calculate tax amount for each tax and validate it with difference of 1
        $tax_amount = $rate->amount * $this->getTaxableAmount() / 100;
        if (abs($this->taxes[$index]['total_tax'] - $tax_amount) > 1) {
          $validator->errors()->add('taxes.' . $index . '.total_tax', 'Tax amount should be between ' . ($tax_amount - 1) . ' and ' . ($tax_amount + 1));
        }
      }
    });
  }
}
