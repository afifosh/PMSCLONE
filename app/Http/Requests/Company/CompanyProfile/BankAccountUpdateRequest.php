<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BankAccountUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:30',
            'branch' => 'required|string|max:30',
            'street' => 'required|string|max:30',
            'city' => 'required|string|max:30',
            'state' => 'required|string|max:30',
            'post_code' => 'required|string|max:30',
            'account_no' => 'required|string|max:30',
            'iban_no' => 'required|string|max:30',
            'swift_code' => 'required|string|max:30',
            'is_authorized' => 'string|nullable',
            'poa' => Rule::requiredIf(function () {
              return $this->is_authorized && !request()->bank_account;
            }),
        ];
    }

    public function messages()
    {
      return [
        'country_id.required' => 'Country is required',
        'name.required' => 'Name is required',
        'branch.required' => 'Branch is required',
        'street.required' => 'Street is required',
        'city.required' => 'City is required',
        'state.required' => 'State is required',
        'post_code.required' => 'Post Code is required',
        'account_no.required' => 'Account No is required',
        'iban_no.required' => 'IBAN No is required',
        'swift_code.required' => 'Swift Code is required',
      ];
    }
}
