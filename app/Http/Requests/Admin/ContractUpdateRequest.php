<?php

namespace App\Http\Requests\Admin;

use App\Models\Contract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContractUpdateRequest extends FormRequest
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
      'subject' => 'required|string|max:100|unique:contracts,subject,' . $this->contract->id . ',id,deleted_at,NULL,project_id,' . $this->contract->project_id,
      'type_id' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'exists:contract_types,id'],
      'assign_to' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'in:Client,Company'],
      'client_id' => ['nullable', Rule::requiredIf($this->assign_to == 'Client' && !$this->isSavingDraft), 'exists:clients,id'],
      'company_id' => ['nullable', Rule::requiredIf($this->assign_to == 'Company' && !$this->isSavingDraft), 'exists:companies,id'],
      'refrence_id' => 'nullable|unique:contracts,refrence_id,'.$this->contract->id.',id,deleted_at,NULL',
      'project_id' => ['nullable', 'exists:projects,id'],
      'start_date' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'date'],
      'end_date' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'date', 'after_or_equal:start_date'],
      'value' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft')],
      'description' => 'nullable|string|max:2000'
    ];
  }

  public function messages()
  {
    return [
      'type_id.required_if' => 'Please select contract type',
      'type_id.required' => 'Please select contract type',
      'assign_to.required_if' => 'Please select assign to',
      'assign_to.required' => 'Please select assign to',
      'client_id.required_if' => 'Please select client',
      'client_id.required' => 'Please select client',
      'company_id.required_if' => 'Please select company',
      'company_id.required' => 'Please select company',
      'project_id.required_if' => 'Please select project',
      'project_id.required' => 'Please select project',
      'start_date.required_if' => 'Please select start date',
      'start_date.required' => 'Please select start date',
      'end_date.required_if' => 'Please select end date',
      'end_date.required' => 'Please select end date',
      'value.required_if' => 'Please enter value',
      'value.required' => 'Please enter value',
    ];
  }
}
