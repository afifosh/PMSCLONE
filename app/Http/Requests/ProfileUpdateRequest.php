<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
          'profile' => 'sometimes|mimetypes:image/*',
          'first_name' => 'required|string|max:255',
          'last_name' => 'required|string|max:255',
          'phone' => 'phone',
          'phone_country' => 'required_with:phone',
          'address' => 'nullable|string|max:255',
          'zip_code' => 'nullable|string|max:8',
          'country_id' => 'nullable|exists:countries,id',
          'state_id' => 'nullable|exists:states,id',
          'city_id' => 'nullable|exists:cities,id',
          'language' => 'nullable|string|max:255',
          'timezone' => 'nullable|string|max:255',
          'currency' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */

    public function messages(): array
    {
        return [
          'profile.mimetypes' => 'Profile image must be an image file.',
          'phone.phone' => 'Phone number is invalid.',
          'phone_country.required_with' => 'Phone number is invalid.',
          'country_id.required' => 'Country is required.',
        ];
    }
}
