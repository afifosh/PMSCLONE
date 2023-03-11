<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

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
            'submit_type' => 'required|string',
            'bank_accounts' => 'required_if:submit_type,submit|array',
            'bank_accounts.*.id' => 'nullable|exists:bank_accounts,id',
            'bank_accounts.*.country_id' => 'required_if:submit_type,submit|nullable|exists:countries,id',
            'bank_accounts.*.name' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.branch' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.street' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.city' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.state' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.post_code' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.account_no' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.iban_no' => 'required_if:submit_type,submit|nullable|string|max:30',
            'bank_accounts.*.swift_code' => 'required_if:submit_type,submit|nullable|string|max:30',
        ];
    }

    public function messages()
    {
      return [
        'bank_accounts.*.*.required_if' => 'This field is required.',
      ];
    }
}
