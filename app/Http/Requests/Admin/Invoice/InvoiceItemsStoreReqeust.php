<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceItemsStoreReqeust extends FormRequest
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
    if (request()->type == 'retentions')
      return [
        'retentions' => 'required|array',
        'retentions.*' => 'nullable|exists:invoices,id|'. Rule::notIn([request()->invoice->id]),
      ];

    return [
      'phases' => 'required|array',
      'phases.*' => 'nullable|exists:contract_phases,id',
    ];
  }
}
