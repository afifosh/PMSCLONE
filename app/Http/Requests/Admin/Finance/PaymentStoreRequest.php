<?php

namespace App\Http\Requests\Admin\Finance;

use App\Models\AuthorityInvoice;
use App\Models\Invoice;
use App\Rules\AccountHasHolder;
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

  public $requiredPermissions = [];

  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation()
  {
    $this->invoice = $this->invoice_type == 'Invoice' ? Invoice::findOrFail($this->invoice_id) : AuthorityInvoice::findOrFail($this->invoice_id);
    $this->payableAmount = $this->invoice->payableAmount();
    if ($this->method() == 'PUT') {
      $this->payableAmount += $this->payment->amount;
    }

    if ($this->payment_type == 'Full') {
      $this->merge([
        'amount' => $this->payableAmount,
      ]);

      if ($this->invoice_type == 'Invoice') {
        $this->requiredPermissions = [1];
      } else {
        $this->requiredPermissions = [2, 3];
      }
    } elseif ($this->invoice_type != 'Invoice' && $this->payment_type == 'wht') {
      $this->merge([
        'amount' => $this->invoice->total_wht,
      ]);
      $this->requiredPermissions = [3];
    } elseif ($this->invoice_type != 'Invoice' && $this->payment_type == 'rc') {
      $this->merge([
        'amount' => $this->invoice->total_rc,
      ]);
      $this->requiredPermissions = [2];
    } elseif ($this->invoice_type == 'Invoice' && $this->payment_type == 'Partial') {
      $this->requiredPermissions = [1];
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
      'payment_type' => 'required|in:Full,Partial,wht,rc',
      'account_balance_id' => [
        'required',
        'exists:account_balances,id',
        new AccountHasHolder(
          ($this->invoice_type == 'Invoice' ? $this->invoice->contract->program_id : $this->invoice->invoice->contract->program_id),
          'programs',
          $this->requiredPermissions
        )
      ],
      'amount' => 'required|numeric|gt:0|lte:' . $this->payableAmount,
      'release_retention' => 'required|in:None,This,All',
      'note' => 'nullable|string',
    ];
  }

  // after validation hook
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->invoice->status == 'Void') {
        $validator->errors()->add('invoice_id', 'Unable to pay. Invoice is voided.');
      }
    });
  }
}
