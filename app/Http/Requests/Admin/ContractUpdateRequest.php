<?php

namespace App\Http\Requests\Admin;

use App\Models\Contract;
use App\Rules\AccountHasHolder;
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
      'subject' => 'required|string',//|max:100|unique:contracts,subject,' . $this->contract->id . ',id,deleted_at,NULL,project_id,' . $this->contract->project_id,
      'type_id' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'exists:contract_types,id'],
      'company_id' => ['nullable', Rule::requiredIf(!$this->isSavingDraft), 'exists:companies,id'],
      'category_id' => 'nullable|required_if:isSavingDraft,0|exists:contract_categories,id',
      'currency' => [Rule::In(array_keys(config('money.currencies'))), 'required_if:isSavingDraft,0'],
      'value' => ['nullable', 'numeric', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'min:0', 'max:92233720368547758'],
      'signature_date' => 'nullable|date',
      'refrence_id' => 'nullable|unique:contracts,refrence_id,'.$this->contract->id.',id,deleted_at,NULL',
      'project_id' => ['nullable', 'exists:projects,id'],
      'program_id' => ['nullable', 'exists:programs,id'],
      'invoicing_method' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'in:Recuring,Phase Based'],
      'account_balance_id' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'exists:account_balances,id', new AccountHasHolder($this->program_id, 'programs')],
      'start_date' => ['nullable', Rule::requiredIf(!$this->isSavingDraft || $this->contract->status != 'Draft'), 'date'],
      'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
      'visible_to_client' => 'nullable|boolean',
      'description' => 'nullable|string|max:2000'
    ];
  }

  public function messages()
  {
    return [
      'type_id.required_if' => 'Please select contract type',
      'type_id.required' => 'Please select contract type',
      'assign_to.required_if' => 'Please select assign to',
      'company_id.required_if' => 'Please select company',
      'company_id.required' => 'Please select company',
      'project_id.required_if' => 'Please select project',
      'project_id.required' => 'Please select project',
      'program_id.required_if' => 'Please select program',
      'program_id.required' => 'Please select program',
      'signature_date.required_if' => 'Please select signature date',
      'signature_date.required' => 'Please select signature date',
      'currency.required_if' => 'Please select currency',
      'currency.required' => 'Please select currency',
      'invoicing_method.required_if' => 'Please select invoicing method',
      'invoicing_method.required' => 'Please select invoicing method',
      'account_balance_id.required_if' => 'Please select account balance',
      'account_balance_id.required' => 'Please select account balance',
      'start_date.required_if' => 'Please select start date',
      'start_date.required' => 'Please select start date',
      'end_date.required_if' => 'Please select end date',
      'end_date.required' => 'Please select end date',
      'value.required_if' => 'Please enter value',
      'value.required' => 'Please enter value',
      'category_id.required_if' => 'Please select contract category'
    ];
  }
}
