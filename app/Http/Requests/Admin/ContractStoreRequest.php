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
      'subject' => 'required|string|max:100',
      'type_id' => 'nullable|required_if:isSavingDraft,0|exists:contract_types,id',
      'assign_to' => 'nullable|required_if:isSavingDraft,0|in:Client,Company',
      'client_id' => ['nullable', Rule::requiredIf($this->assign_to == 'Client' && $this->isSavingDraft == 0), 'exists:clients,id'],
      'company_id' => ['nullable', Rule::requiredIf($this->assign_to == 'Company' && $this->isSavingDraft == 0), 'exists:companies,id'],
      'project_id' => ['nullable', Rule::requiredIf($this->assign_to == 'Company' && $this->isSavingDraft == 0), 'exists:projects,id'],
      'refrence_id' => 'nullable|unique:contracts,refrence_id',
      'start_date' => 'nullable|required_if:isSavingDraft,0|date',
      'end_date' => 'nullable|required_if:isSavingDraft,0|date|after_or_equal:start_date',
      'value' => 'nullable|required_if:isSavingDraft,0',
      'description' => 'nullable|string|max:2000',
    ];
  }

  public function messages()
  {
    return [
      'type_id.required_if' => 'Please select contract type',
      'assign_to.required_if' => 'Please select assign to',
      'client_id.required_if' => 'Please select client',
      'company_id.required_if' => 'Please select company',
      'project_id.required_if' => 'Please select project',
      'project_id.required' => 'Please select project',
      'start_date.required_if' => 'Please select start date',
      'end_date.required_if' => 'Please select end date',
      'value.required_if' => 'Please enter value',
    ];
  }
}
