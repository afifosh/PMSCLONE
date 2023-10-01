<?php

namespace App\Http\Requests\Admin\ContractDoc;

use App\Models\KycDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocControllStoreRequest extends FormRequest
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
      'title' => ['required', 'string'],
      'client_type' => ['required', 'in:Person,Company,Both'],
      'contract_type_ids' => ['nullable', 'array'],
      'contract_type_ids.*' => ['nullable', 'exists:contract_types,id'],
      'contract_category_ids' => ['nullable', 'array'],
      'contract_category_ids.*' => ['nullable', 'exists:contract_categories,id'],
      'status' => ['required', 'boolean'],
      'is_mendatory' => ['required', 'boolean'],
      'description' => ['nullable', 'string', 'max:2000'],
      'is_expirable' => ['nullable', 'boolean'],
      'expiry_date_title' => ['required_if:is_expirable,1', 'nullable', 'max:255'],
      'is_expiry_date_required' => ['required_if:is_expirable,1', 'boolean'],
      'fields' => ['required', 'array'],
      'fields.*.is_required' => ['required', 'boolean'],
      'fields.*.label' => ['required', 'string'],
      'fields.*.type' => ['required', 'string', Rule::in(KycDocument::TYPES)],
    ];
  }

  public function messages()
  {
    return [
      'fields.*.label.required' => __('Label is required.'),
      'fields.*.type.required' => __('Type is required.'),
      'fields.*.type.in' => __('Type is invalid.'),
      'fields.*.is_required.required' => __('Required is required.'),
      'contract_type_ids.*.exists' => __('Contract type is invalid.'),
      'contract_category_ids.*.exists' => __('Contract category is invalid.')
    ];
  }
}