<?php

namespace App\Http\Requests\Admin\Finance;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
  public $payableAmount = 0;

  /**
   * @var App\Models\Invoice
   */

  public $invoice;
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation()
  {
    $this->invoice = Invoice::findOrFail($this->invoice_id);
    $this->payableAmount = $this->invoice->total - $this->invoice->paid_amount - $this->invoice->retention_amount;
    if ($this->method() == 'PUT') {
      $this->payableAmount += $this->payment->amount;
    }

    if ($this->payment_type == 'Full') {
      $this->merge([
        'amount' => $this->payableAmount,
      ]);
    }
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'invoice_id' => 'required|exists:invoices,id',
      'transaction_id' => 'required|string',
      'payment_date' => 'required|date',
      'payment_type' => 'required|in:Full,Partial',
      'amount' => 'required|numeric|gt:0|lte:' . $this->payableAmount,
      'release_retention' => 'required|in:None,This,All',
      'note' => 'nullable|string',
    ];
  }
}
