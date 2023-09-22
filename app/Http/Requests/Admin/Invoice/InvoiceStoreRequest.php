<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class InvoiceStoreRequest extends FormRequest
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
    if(request()->update_tax_type)
      return [
        'is_summary_tax' => 'required|in:0,1',
      ];

    if(request()->method() == 'PUT')
      return [
        'invoice_date' => 'required|date',
        'due_date' => 'required|date',
        'note' => 'nullable|string',
        'terms' => 'nullable|string',
      ];

    return [
      'company_id' => 'required|exists:companies,id',
      'contract_id' => 'required|exists:contracts,id',
      'invoice_date' => 'required|date',
      'due_date' => 'required|date',
      'note' => 'nullable|string',
      'terms' => 'nullable|string',
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array<string, string>
   */

  public function messages(): array
  {
    return [
      'company_id.required' => 'Please select a client',
      'company_id.exists' => 'Client is not valid',
      'contract_id.required' => 'Contract is required',
      'contract_id.exists' => 'Contract is not valid',
      'invoice_date.required' => 'Invoice date is required',
      'invoice_date.date' => 'Invoice date is not valid',
      'due_date.required' => 'Due date is required',
      'due_date.date' => 'Due date is not valid',
    ];}
}
