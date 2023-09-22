<?php

namespace App\Http\Requests\Admin\Finance;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class PaymentStoreRequest extends FormRequest
{
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
    $invoice = Invoice::findOrFail($this->invoice_id);
    $remaining = $invoice->total - $invoice->paid_amount;
    if ($this->method() == 'PUT') {
      $remaining += $this->payment->amount;
    }
    return [
      'invoice_id' => 'required|exists:invoices,id',
      'transaction_id' => 'required|string',
      'payment_date' => 'required|date',
      'amount' => 'required|numeric|gt:0|lte:'.$remaining,
      'note' => 'nullable|string',
    ];
  }
}
