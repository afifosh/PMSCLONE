<?php

namespace App\Http\Requests\Admin;

use App\Rules\AccountHasHolder;
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
      'company_id' => ['nullable', 'required_if:isSavingDraft,0', 'exists:companies,id'],
      'project_id' => ['nullable', 'exists:projects,id'],
      'program_id' => ['nullable', 'required_if:isSavingDraft,0', 'exists:programs,id'],
      'signature_date' => 'nullable|date',
      'currency' => [Rule::In(array_keys(config('money.currencies'))), 'required_if:isSavingDraft,0'],
      'value' => ['nullable', 'numeric', 'required_if:isSavingDraft,0', 'gt:0', 'max:92233720368547758'],
      'invoicing_method' => ['nullable', 'required_if:isSavingDraft,0', 'in:Recuring,Phase Based'],
      'account_balance_id' => ['nullable', 'required_if:isSavingDraft,0', 'exists:account_balances,id', new AccountHasHolder($this->program_id, 'programs')],
      'refrence_id' => 'nullable|unique:contracts,refrence_id,NULL,id,deleted_at,NULL',
      'start_date' => 'nullable|required_if:isSavingDraft,0|date',
      'end_date' => 'nullable|date|after_or_equal:start_date',
      'visible_to_client' => 'nullable|boolean',
      'description' => 'nullable|string|max:2000',
    ];
  }

  public function messages()
  {
    return [
      'type_id.required_if' => 'Please select contract type',
      'category_id.required_if' => 'Please select contract category',
      'company_id.required_if' => 'Please select company',
      'project_id.required_if' => 'Please select project',
      'program_id.required_if' => 'Please select program',
      'project_id.required' => 'Please select project',
      'signature_date.required_if' => 'Please select signature date',
      'currency.required_if' => 'Please select currency',
      'start_date.required_if' => 'Please select start date',
      'end_date.required_if' => 'Please select end date',
      'invoicing_method.required_if' => 'Please select invoicing method',
      'account_balance_id.required_if' => 'Please select account balance',
      'value.required_if' => 'Please enter value',
    ];
  }
}
