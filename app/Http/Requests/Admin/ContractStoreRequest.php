<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'isSavingDraft' => 'nullable',
      'subject' => 'required|string',
      'type_id' => 'nullable|required_if:isSavingDraft,0|exists:contract_types,id',
      'category_id' => 'nullable|required_if:isSavingDraft,0|exists:contract_categories,id',
      'company_id' => ['nullable', Rule::requiredIf($this->isSavingDraft == 0), 'exists:companies,id'],
      'project_id' => ['nullable', 'exists:projects,id'],
      'program_id' => ['nullable', 'exists:programs,id'],
      'signature_date' => 'nullable|required_if:isSavingDraft,0|date',
      'currency' => [Rule::In(array_keys(config('money.currencies'))), 'required_if:isSavingDraft,0'],
      'value' => ['nullable', Rule::requiredIf(!$this->isSavingDraft), 'min:0', 'max:92233720368547758'],
      'refrence_id' => 'nullable|unique:contracts,refrence_id',
      'start_date' => 'nullable|required_if:isSavingDraft,0|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      'description' => 'nullable|string|max:2000',
    ];
  }

  public function messages()
  {
    return [
      'type_id.required_if' => 'Please select contract type',
      'company_id.required_if' => 'Please select company',
      'project_id.required_if' => 'Please select project',
      'project_id.required' => 'Please select project',
      'start_date.required_if' => 'Please select start date',
      'end_date.required_if' => 'Please select end date',
      'value.required_if' => 'Please enter value',
    ];
  }
}
