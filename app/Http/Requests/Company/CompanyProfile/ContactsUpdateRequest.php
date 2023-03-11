<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

class ContactsUpdateRequest extends FormRequest
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
            'contacts.*.type' => 'required_if:submit_type,submit|nullable|integer',
            'contacts.*.id' => 'nullable|exists:company_contacts,id',
            'contacts.*.title' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.first_name' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.last_name' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.position' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.phone' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.mobile' => 'required_if:submit_type,submit|nullable|string|max:30',
            'contacts.*.email' => 'required_if:submit_type,submit|nullable|email',
            'contacts.*.fax' => 'required_if:submit_type,submit|nullable|string|max:30',
            // 'contacts.*.poa' => 'required_if:submit_type,submit|nullable|string',
        ];
    }

    public function messages()
    {
      return [
        'contacts.*.*.required_if' => 'This field is required.',
      ];
    }
}
