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
    if(request()->type == 'rounding')
      return [
        'rounding_amount' => 'required|in:0,1',
      ];
    if(request()->update_tax_type)
      return [
        'is_summary_tax' => 'required|in:0,1',
      ];

    elseif(request()->update_discount){
      return [
        'discount_type' => 'required|in:Fixed,Percentage',
        'discount_value' => ['required', 'numeric', function($attribute, $value, $fail){
          if(request()->discount_type == 'Percentage' && ($value > 100 || $value < 0))
            $fail('Discount percentage must be between 0 and 100');
          elseif(request()->discount_type == 'Fixed' && ($value > $this->invoice->subtotal || $value < 0))
            $fail('Discount amount must be between 0 and invoice subtotal');
        }],
      ];
    }

    elseif(request()->update_adjustment){
      return [
        'adjustment_description' => 'required|string|max:255',
        'adjustment_amount' => ['required', 'numeric'],
      ];
    }

    elseif(request()->update_retention){
      return [
        'retention_id' => 'nullable|exists:invoice_configs,id',
        // 'retention_type' => 'required|in:Fixed,Percentage',
        // 'retention_value' => ['nullable', 'numeric', function($attribute, $value, $fail){
        //   if(request()->retention_type == 'Percentage' && ($value > 100 || $value < 0))
        //     $fail('Retention percentage must be between 0 and 100');
        //   elseif(request()->retention_type == 'Fixed' && ($value > $this->invoice->subtotal || $value < 0))
        //     $fail('Retention amount must be between 0 and invoice subtotal');
        // }],
      ];
    }

    elseif(request()->type == 'downpayment')
      return [
        'subtotal' => 'required|numeric|gt:0',
        'description' => 'nullable|string|max:255',
      ];

    if(request()->method() == 'PUT')
      return [
        'invoice_date' => 'required|date',
        'due_date' => 'required|date',
        'note' => 'nullable|string',
        'terms' => 'nullable|string',
        'refrence_id' => 'nullable|string|max:255'
      ];

    return [
      'type' => 'required|in:Regular,Down Payment,Partial Invoice',
      'subtotal' => 'nullable|required_if:type,Down Payment|numeric'.(request()->type == 'Down Payment' ? '|gt:0' : ''),
      'description' => 'nullable|required_if:type,Down Payment|string|max:255',
      'company_id' => 'required|exists:companies,id',
      'contract_id' => 'required|exists:contracts,id',
      'invoice_date' => 'required|date',
      'due_date' => 'required|date',
      'note' => 'nullable|string',
      'terms' => 'nullable|string',
      'refrence_id' => 'nullable|string|max:255',
      'phase_id' => 'nullable|required_if:type,Partial Invoice|exists:contract_phases,id'
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
      'subtotal.required_if' => 'Subtotal is required',
      'subtotal.numeric' => 'Subtotal must be a number',
      'subtotal.gt' => 'Subtotal must be greater than 0',
      'discription.required_if' => 'Description is required',
    ];}
}
