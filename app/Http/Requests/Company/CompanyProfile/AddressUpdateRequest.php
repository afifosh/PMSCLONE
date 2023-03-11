<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
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
            'addresses' => 'required_if:submit_type,submit|array',
            'addresses.*.id' => 'nullable|exists:company_addresses,id',
            'addresses.*.name' => 'required_if:submit_type,submit|nullable|string|max:255',
            'addresses.*.country_id' => 'required_if:submit_type,submit|nullable|exists:countries,id',
            'addresses.*.address_line_1' => 'required_if:submit_type,submit|nullable|string|max:255',
            'addresses.*.address_line_2' => 'required_if:submit_type,submit|nullable|string|max:255',
            'addresses.*.address_line_3' => 'required_if:submit_type,submit|nullable|string|max:255',
            'addresses.*.website' => 'required_if:submit_type,submit|nullable|url|max:50',
            'addresses.*.city' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.state' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.province' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.postal_code' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.zip' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.phone' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.fax' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.email' => 'required_if:submit_type,submit|nullable|email|max:30',
            'addresses.*.latitude' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.longitude' => 'required_if:submit_type,submit|nullable|string|max:30',
            'addresses.*.address_type' => 'nullable|array',
            'addresses.*.address_type.*' => 'nullable|string',
        ];
    }

    public function messages()
    {
      return [
        'addresses.*.*.required_if' => 'This field is required.',
      ];
    }
}
