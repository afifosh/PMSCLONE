<?php

namespace App\Http\Requests\Admin\Contract\Phase;

use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;

class TaxStoreRequest extends FormRequest
{
  /**
   * The tax rate to be added. will be used in validation and controller as well
   */
  public $tax_rate = null;

  /**
   * calculated tax amount for the phase
   */
  public $calculated_tax_amount = 0;

  public $calculated_downpayment_amount = 0;

  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation(): void
  {
    $this->merge([
      'is_manual_tax' => $this->boolean('is_manual_tax'),
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'tax' => 'required|exists:invoice_configs,id,config_type,tax',
      'total_tax' => 'nullable|numeric|gte:0',
      'is_manual_tax' => 'required|boolean',
    ];
  }

  // after validation hook
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      // calculate total tax amount
      $this->calCulateTaxAmount();
      // total tax amount should be +-1 of calculated tax amount
      if ($this->is_manual_tax && abs($this->total_tax - $this->calculated_tax_amount) > 1) {
        $validator->errors()->add('total_tax', 'Total tax amount should be between ' . ($this->calculated_tax_amount - 1) . ' and ' . ($this->calculated_tax_amount + 1));
      }
    });
  }

  public function calCulateTaxAmount()
  {
    $this->tax_rate = InvoiceConfig::activeTaxes()->findOrFail($this->tax);

    if ($this->tax_rate->type != 'Fixed') {
      $this->calculated_tax_amount = $this->getTaxbleAmount() * $this->tax_rate->amount / 100;
    } else {
      $this->calculated_tax_amount = $this->tax_rate->amount;
    }
  }

  public function getTaxbleAmount()
  {
    $this->phase->load('deduction');
    if ($this->phase->deduction && $this->phase->deduction->is_before_tax) {
      $this->calDeductionAmount();
      return $this->phase->estimated_cost - $this->calculated_downpayment_amount;
    }

    return $this->phase->estimated_cost;
  }

  private function calDeductionAmount(): void
  {
    $this->calculated_downpayment_amount = 0;

    if (!$this->phase->deduction) {
      return;
    }

    if (!$this->phase->deduction->is_percentage || $this->phase->deduction->calculation_source == 'Down Payment') {
      $this->calculated_downpayment_amount = $this->phase->deduction->amount;
      return;
    }

    $this->calculated_downpayment_amount = ($this->phase->estimated_cost * $this->phase->deduction->percentage) / 100;
  }
}
