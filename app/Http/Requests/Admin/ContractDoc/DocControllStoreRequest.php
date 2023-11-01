<?php

namespace App\Http\Requests\Admin\ContractDoc;

use App\Models\Invoice;
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

  public function prepareForValidation()
  {
    $this->merge([
      'signable' => $this->boolean('signable'),
      'stampable' => $this->boolean('stampable'),
      'signatures_required' =>  $this->signable ? $this->signatures_required ?? 0 : 0,
      'stamps_required' => $this->stampable ? $this->stamps_required ?? 0 : 0,
      'having_refrence_id' => $this->boolean('having_refrence_id'),
    ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $rules = [
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
      'invoice_type' => ['nullable', 'sometimes', Rule::in(Invoice::TYPES)],
      'required_at' => ['nullable', 'date'],
      'required_at_type' => ['string', Rule::in(['Before', 'After', 'On'])],
      'signable' => ['required', 'boolean'],
      'signatures_required' => ['nullable', 'integer', 'min:0'],
      'stampable' => ['required', 'boolean'],
      'stamps_required' => ['nullable', 'integer', 'min:0'],
      'having_refrence_id' => ['required', 'boolean'],
    ];

    if($this->route()->getName() == 'admin.invoice-doc-controls.store'){
      $rules['contract_ids'] = ['nullable', 'array'];
      $rules['contract_ids.*'] = ['nullable', 'exists:contracts,id'];
    }

    return $rules;
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
