<?php

namespace App\Http\Requests\Admin\Contract;

use App\Models\Company;
use App\Models\PartnerCompany;
use Illuminate\Foundation\Http\FormRequest;

class ContractPartyRequest extends FormRequest
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
    $contract_party_types = ['Company' => Company::class, 'Client' => Company::class, 'PartnerCompany' => PartnerCompany::class];
    $this->merge([
      'contract_party_type' =>  isset($contract_party_types[$this->contract_party_type]) ? $contract_party_types[$this->contract_party_type] : null,
    ]);

    if($this->method() == 'POST'){
      $this->merge([
        'added_by' => auth()->user()->id,
      ]);
    }
  }
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $rules = [
      'contract_party_type' => 'required',
      'contract_party_id' => 'required|exists:' . ($this->contract_party_type == Company::class ? 'companies,id' : 'partner_companies,id'),
    ];

    if($this->method() == 'POST'){
      $rules['added_by'] = 'required';
    }

    return $rules;
  }
}
