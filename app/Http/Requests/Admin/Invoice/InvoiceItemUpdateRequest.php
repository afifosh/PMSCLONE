<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemUpdateRequest extends FormRequest
{
  /**
   * Taxes
   */
  public $taxes = [];

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
  public $downpayment_amount = 0;

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation(): void
  {
    $this->merge([
      'subtotal' => $this->invoice_item->invoiceable->estimated_cost,
      /** Deduction Related Fields */
      'deduct_downpayment' => $this->boolean('deduct_downpayment'),
      'is_before_tax' => $this->boolean('is_before_tax'),
      'is_manual_deduction' => $this->boolean('is_manual_deduction'),
      'manual_deduction_amount' => $this->is_manual_deduction ? $this->manual_deduction_amount : 0,
      /** End Deduction Related Fields */
      'add_tax' => $this->boolean('add_tax'),
      'is_manual_tax' => $this->boolean('is_manual_tax'),
      'manual_tax_amount' => $this->is_manual_tax ? $this->manual_tax_amount : 0,
      'rounding_amount' => $this->boolean('rounding_amount'),
    ]);

    // validate
    $this->validate([
      'phase_id' => 'required|exists:contract_phases,id',
      'subtotal' => 'required|numeric|gt:0',
      // deduction fields
      'deduct_downpayment' => 'required|boolean',
      'is_before_tax' => 'required|boolean',
      'downpayment_id' => 'nullable|required_if:deduct_downpayment,true|exists:invoices,id',
      //'downpayment_amount' => 'nullable|required_with:downpayment_id|numeric|gte:0' . ($this->downpayment ? '|max:' . $this->downpayment->downpaymentAmountRemaining() : ''),
      'dp_rate_id' => 'nullable|required_if:deduct_downpayment,true|exists:invoice_configs,id',
      'calculation_source' => 'nullable|required_if:deduct_downpayment,true|in:Down Payment,Deductible',
      'is_manual_deduction' => 'required|boolean',
      'manual_deduction_amount' => 'nullable|required_if:is_manual_deduction,true|numeric|gte:0',
      // tax fields
      'add_tax' => 'required|boolean',
      'item_taxes' => 'nullable|array|required_if:add_tax,true',
      'item_taxes.*' => 'nullable|required_if:add_tax,true|exists:invoice_configs,id',
      //'total_tax_amount' => 'nullable|required_if:add_tax,true|numeric|gt:0',
      //'total' => 'required|numeric|gt:0',
      'manual_tax_amount' => 'nullable|required_if:is_manual_tax,true',
    ], $this->messaages());
    $configs = InvoiceConfig::activeOnly()->whereIn('id', array_merge(filterInputIds($this->item_taxes), [$this->dp_rate_id]))->get();
    $this->taxes = $configs->where('config_type', 'Tax');
    $this->deduction_rate = $configs->where('id', $this->dp_rate_id)->first();
    unset($configs);

    $this->downpayment = Invoice::find($this->downpayment_id);

    $this->calTaxAmount();
    $this->calDeductionAmount();

    $this->merge([
      'total_tax_amount' => $this->total_tax_amount,
      'downpayment_amount' => $this->downpayment_id ? $this->downpayment_amount : 0,
      'total' => $this->subtotal + ($this->is_manual_tax ? $this->manual_tax_amount : $this->total_tax_amount) - ($this->is_manual_deduction ? $this->manual_deduction_amount : $this->downpayment_amount),
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
  public function rules(): array
  {
    $rules = [
      'phase_id' => 'required|exists:contract_phases,id',
      'subtotal' => 'required|numeric|gt:0',
      // deduction fields
      'deduct_downpayment' => 'required|boolean',
      'is_before_tax' => 'required|boolean',
      'downpayment_id' => 'nullable|exists:invoices,id',
      'downpayment_amount' => 'nullable|required_with:downpayment_id|numeric|gte:0' . ($this->downpayment ? '|max:' . $this->downpayment->downpaymentAmountRemaining() : ''),
      'dp_rate_id' => 'nullable|required_if:deduct_downpayment,true|exists:invoice_configs,id',
      'calculation_source' => 'nullable|required_if:deduct_downpayment,true|in:Down Payment,Deductible',
      'is_manual_deduction' => 'required|boolean',
      'manual_deduction_amount' => 'nullable|required_with:is_manual_deduction|numeric|gte:0',
      // tax fields
      'add_tax' => 'required|boolean',
      'item_taxes' => 'nullable|array',
      'item_taxes.*' => 'nullable|exists:invoice_configs,id',
      'total_tax_amount' => 'nullable|required_if:add_tax,true|numeric',
      'total' => 'required|numeric|gt:0',
      'manual_tax_amount' => 'required|numeric',
      'rounding_amount' => 'required'
    ];

    if ($this->is_manual_tax) {
      // manual tax should be between +-1 of calculated tax amount
      $rules['manual_tax_amount'] = 'required|numeric|between:' . ($this->total_tax_amount - 1) . ',' . ($this->total_tax_amount + 1);
    }

    if ($this->is_manual_deduction) {
      // manual deduction should be between +-1 of calculated deduction amount
      $rules['manual_deduction_amount'] = 'required|numeric|between:' . ($this->downpayment_amount - 1) . ',' . ($this->downpayment_amount + 1);
    }

    return $rules;
  }

  private function calDeductionAmount(): void
  {
    $this->downpayment_amount = 0;

    if ($this->deduct_downpayment) {
      if ($this->deduction_rate->type == 'Fixed') {
        $this->downpayment_amount = $this->deduction_rate->amount;
        return;
      }
      if ($this->calculation_source == 'Down Payment') {
          $this->downpayment_amount = ($this->downpayment->total * $this->deduction_rate->amount) / 100;
      } else {
        if($this->is_before_tax) {
          $this->downpayment_amount = ($this->subtotal * $this->deduction_rate->amount) / 100;
        } else {
          $this->downpayment_amount = ($this->subtotal + ($this->is_manual_tax ? $this->manual_tax_amount : $this->total_tax_amount) * $this->deduction_rate->amount) / 100;
        }
      }
    }
  }

  private function calTaxAmount(): void
  {
    $this->total_tax_amount = 0;
    if(!$this->add_tax) {
      return;
    }
    if($this->is_before_tax) {
      $this->calDeductionAmount();
    }
    foreach ($this->taxes as $tax) {
      if ($tax->type == 'Fixed') {
        $this->total_tax_amount += $tax->amount;
      } else {
        $this->total_tax_amount += ($this->taxableAmount() * $tax->amount) / 100;
      }
    }
  }

  private function taxableAmount()
  {
    if ($this->is_before_tax) {
      return $this->subtotal - ($this->is_manual_deduction ? $this->manual_deduction_amount : $this->downpayment_amount);
    }

    return $this->subtotal;
  }

  /**
   * Get the validation messages that apply to the request.
   *
   * @return array<string, string>
   */
  public function messaages(): array
  {
    return [
      'name.required' => 'Item name is required',
      'name.string' => 'Item name must be a string',
      'name.max' => 'Item name must not be greater than 255 characters',
      'price.required' => 'Item price is required',
      'price.numeric' => 'Item price must be a number',
      'price.gt' => 'Item price must be greater than 0',
      'quantity.required' => 'Item quantity is required',
      'quantity.numeric' => 'Item quantity must be a number',
      'quantity.gt' => 'Item quantity must be greater than 0',
      'subtotal.required' => 'Item subtotal is required',
      'subtotal.numeric' => 'Item subtotal must be a number',
      'subtotal.gt' => 'Item subtotal must be greater than 0',
      // deduction fields
      'deduct_downpayment.required' => 'Deduct downpayment is required',
      'deduct_downpayment.boolean' => 'Deduct downpayment must be a boolean',
      'is_before_tax.required' => 'Deduct downpayment before tax is required',
      'is_before_tax.boolean' => 'Deduct downpayment before tax must be a boolean',
      'downpayment_id.exists' => 'Downpayment does not exist',
      'downpayment_amount.required_with' => 'Downpayment amount is required',
      'downpayment_amount.numeric' => 'Downpayment amount must be a number',
      'downpayment_amount.gte' => 'Downpayment amount must be greater than or equal to 0',
      'downpayment_amount.max' => 'Downpayment amount must not be greater than downpayment amount remaining',
      'dp_rate_id.required_if' => 'Downpayment rate is required',
      'dp_rate_id.exists' => 'Downpayment rate does not exist',
      'calculation_source.required_if' => 'Calculation source is required',
      'calculation_source.in' => 'Calculation source must be one of Down Payment or Deductible',
      'is_manual_deduction.required' => 'Manual deduction is required',
      'is_manual_deduction.boolean' => 'Manual deduction must be a boolean',
      'manual_deduction_amount.required_if' => 'Manual deduction amount is required',
      'manual_deduction_amount.numeric' => 'Manual deduction amount must be a number',
      'manual_deduction_amount.gte' => 'Manual deduction amount must be greater than or equal to 0',
      // tax fields
      'add_tax.required' => 'Add tax is required',
      'add_tax.boolean' => 'Add tax must be a boolean',
      'item_taxes.array' => 'Item taxes must be an array',
      'item_taxes.*.required_if' => 'Item taxes is required',
      'item_taxes.*.exists' => 'Item tax does not exist',
      'total_tax_amount.required_if' => 'Total tax amount is required',
      'total_tax_amount.numeric' => 'Total tax amount must be a number',
      'total_tax_amount.gt' => 'Total tax amount must be greater than 0',
      'total.required' => 'Total is required',
      'total.numeric' => 'Total must be a number',
      'total.gt' => 'Total must be greater than 0',
      'manual_tax_amount.required_if' => 'Manual tax amount is required',
      'manual_tax_amount.numeric' => 'Manual tax amount must be a number',
    ];
  }

  // post validation hook
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->is_before_tax) {
        if ($this->downpayment_amount > $this->subtotal) {
          $validator->errors()->add('downpayment_amount', 'Downpayment amount must not be greater than subtotal');
        }
      } else {
        if ($this->downpayment_amount > $this->subtotal + ($this->is_manual_tax ? $this->manual_tax_amount : $this->total_tax_amount)) {
          $validator->errors()->add('downpayment_amount', 'Downpayment amount must not be greater than subtotal + tax');
        }
      }
    });
  }
}
