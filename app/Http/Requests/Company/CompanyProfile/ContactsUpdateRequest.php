<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'type' => 'required|integer',
            'title' => 'required|string|max:30',
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'position' => 'required|string|max:30',
            'phone' => 'required|string|max:30',
            'mobile' => 'required|string|max:30',
            'email' => 'required|email',
            'fax' => 'required|string|max:30',
            'is_authorized' => 'string|nullable',
            'poa' => Rule::requiredIf(function () {
              return $this->is_authorized && !request()->contact;
            }),
            // 'required_if:is_authorized,on'
        ];
    }

    public function messages()
    {
      return [
        'type.required' => 'Contact Type is required',
        'title.required' => 'Title is required',
        'first_name.required' => 'First Name is required',
        'last_name.required' => 'Last Name is required',
        'position.required' => 'Position is required',
        'phone.required' => 'Phone is required',
        'mobile.required' => 'Mobile is required',
        'email.required' => 'Email is required',
        'fax.required' => 'Fax is required',
        'poa.required_if' => 'POA is required',
      ];
    }
}
