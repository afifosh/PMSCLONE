<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ContractStoreRequest extends FormRequest
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
    return [
      'subject' => 'required|string|max:100',
      'type_id' => 'required|exists:contract_types,id',
      'company_id' => 'required|exists:companies,id',
      'project_id' => 'required|exists:projects,id',
      'start_date' => 'required|date',
      'end_date' => 'required|date|after_or_equal:start_date',
      'value' => 'required',
      'description' => 'nullable|string|max:1000',
    ];
  }
}
