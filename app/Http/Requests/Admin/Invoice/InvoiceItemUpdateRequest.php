<?php

namespace App\Http\Requests\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceItemUpdateRequest extends FormRequest
{
  /**
   * Taxes
   */
  public $taxes;

  /**
   * Total Tax Amount
   */
  public $total_tax_amount = 0;

  /**
   * Downpayment
   */
  public $downpayment;

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation(): void
  {
    $this->taxes = Tax::where('is_retention', 0)->where('status', 'Active')->whereIn('id', filterInputIds($this->item_taxes))->get();
    $this->downpayment = Invoice::find($this->downpayment_id);
    $this->calTaxAmount();
    $this->merge([
      'subtotal' => $this->invoice_item->invoiceable->estimated_cost,
      'is_manual_tax' => $this->boolean('is_manual_tax'),
      'manual_tax_amount' => $this->is_manual_tax ? $this->manual_tax_amount : 0,
      'downpayment_amount' => $this->downpayment_id ? $this->downpayment_amount : 0,
    ]);

    $this->merge([
      'total' => $this->subtotal + ($this->is_manual_tax ? $this->manual_tax_amount : $this->total_tax_amount) - $this->downpayment_amount,
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
      'downpayment_id' => 'nullable|exists:invoices,id',
      'downpayment_amount' => 'nullable|required_with:downpayment_id|numeric|gte:0' . ($this->downpayment ? '|max:' . $this->downpayment->downpaymentAmountRemaining() : ''),
      'item_taxes' => 'nullable|array',
      'item_taxes.*' => 'nullable|exists:taxes,id',
      'total_tax_amount' => 'required|numeric|gte:0',
      'total' => 'required|numeric|gt:0',
      'manual_tax_amount' => 'required|numeric',
    ];

    if ($this->is_manual_tax) {
      // manual tax should be between +-1 of calculated tax amount
      $rules['manual_tax_amount'] = 'required|numeric|between:' . ($this->total_tax_amount - 1) . ',' . ($this->total_tax_amount + 1);
    }

    return $rules;
  }

  private function calTaxAmount(): void
  {
    $this->total_tax_amount = 0;
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
    return $this->subtotal - $this->downpayment_amount;
  }
}
