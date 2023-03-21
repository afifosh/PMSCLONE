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
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'required|string|max:255',
            'address_line_3' => 'required|string|max:255',
            'website' => 'required|url|max:50',
            'city' => 'required|string|max:30',
            'state' => 'required|string|max:30',
            'province' => 'required|string|max:30',
            'postal_code' => 'required|string|max:30',
            'zip' => 'required|string|max:30',
            'phone' => 'required|string|max:30',
            'fax' => 'required|string|max:30',
            'email' => 'required|email|max:30',
            'latitude' => 'required|string|max:30',
            'longitude' => 'required|string|max:30',
            'address_type' => 'nullable|array',
            'address_type.*' => 'nullable|string',
        ];
    }

    public function messages()
    {
      return [
        'name.required' => 'Name is required',
        'country_id.required' => 'Country is required',
        'address_line_1.required' => 'Address Line 1 is required',
        'address_line_2.required' => 'Address Line 2 is required',
        'address_line_3.required' => 'Address Line 3 is required',
        'website.required' => 'Website is required',
        'city.required' => 'City is required',
        'state.required' => 'State is required',
        'province.required' => 'Province is required',
        'postal_code.required' => 'Postal Code is required',
        'zip.required' => 'Zip is required',
        'phone.required' => 'Phone is required',
        'fax.required' => 'Fax is required',
        'email.required' => 'Email is required',
        'latitude.required' => 'Latitude is required',
        'longitude.required' => 'Longitude is required',
        'address_type.required' => 'Address Type is required',
      ];
    }
}
