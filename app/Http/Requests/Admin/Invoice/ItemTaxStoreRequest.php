<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\InvoiceConfig;
use Illuminate\Foundation\Http\FormRequest;

class ItemTaxStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Taxes
   */
  public $tax;

  /**
   * Total Tax Amount
   */
  public $calculated_tax_amount = 0;

  /**
   * Deduction Amount
   */
  public $downpayment_amount = 0;

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  public function prepareForValidation()
  {
    $this->merge([
      'subtotal' => $this->invoice_item->subtotal,
      'rounding_amount' => $this->boolean('rounding_amount'),
      'is_manual_tax' => $this->boolean('is_manual_tax'),
    ]);

    // validate
    $this->validate($this->getItemRules(), $this->messaages());
    $this->tax = InvoiceConfig::activeOnly()->findOrFail($this->item_tax);

    $this->calTaxAmount();
    $this->calDeductionAmount();

    $this->merge([
      'manual_tax_amount' => ($this->is_manual_tax && $this->calculated_tax_amount != $this->total_tax_amount) ? $this->total_tax_amount : 0,
    ]);
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
      // tax fields
      'item_tax' => 'required|exists:invoice_configs,id',
      'total_tax_amount' => 'nullable|numeric',
      // 'manual_tax_amount' => 'nullable|required_if:is_manual_tax,true|numeric',
    ];
  }
  public function rules(): array
  {
    $rules = $this->getItemRules() + [
      // // tax fields
      'rounding_amount' => 'required'
    ];

    if ($this->is_manual_tax) {
      // manual tax should be between +-1 of calculated tax amount
      $rules['total_tax_amount'] = 'required|numeric|between:' . ($this->calculated_tax_amount - 1) . ',' . ($this->calculated_tax_amount + 1);
    }

    return $rules;
  }

  private function calDeductionAmount(): void
  {
    if($this->invoice_item->deduction && $this->invoice_item->deduction->is_before_tax) {
      $this->downpayment_amount = ($this->invoice_item->deduction->manual_amount ? $this->invoice_item->deduction->manual_amount : $this->invoice_item->deduction->amount);
    }
      return;
  }

  private function calTaxAmount(): void
  {
    $this->calculated_tax_amount = 0;
    $this->invoice_item->load('deduction');
    $this->downpayment_amount = 0;
    if($this->invoice_item->deduction && $this->invoice_item->deduction->is_before_tax) {
      $this->calDeductionAmount();
    }
    if ($this->tax->type == 'Fixed') {
      $this->calculated_tax_amount += $this->tax->amount;
    } else {
      $this->calculated_tax_amount += ($this->taxableAmount() * $this->tax->amount) / 100;
    }
  }

  private function taxableAmount()
  {
    if ($this->invoice_item->deduction && $this->invoice_item->deduction->is_before_tax) {
      return $this->subtotal - $this->downpayment_amount;
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
    return [];
  }

  // post validation hook
  // public function withValidator($validator)
  // {
  //   $validator->after(function ($validator) {
  //     if ($this->is_manual_tax && $this->manual_tax_amount == 0) {
  //       $validator->errors()->add('manual_tax_amount', 'Manual Tax Amount is required');
  //     }
  //   });
  // }
}
