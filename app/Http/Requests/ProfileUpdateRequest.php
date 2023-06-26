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
          'email' => [
            'required',
            'email',
            'max:255',
            request()->path('admin/*') ? 'unique:admins,email,'. auth()->id() : 'unique:users,email,'. auth()->id()
          ],
          'phone' => 'required|string|max:255',
          'address' => 'required|string|max:255',
          'state' => 'required|string|max:255',
          'zip_code' => 'required|string|max:255',
          'country_id' => 'required|exists:countries,id',
          'language' => 'required|string|max:255',
          'timezone' => 'required|string|max:255',
          'currency' => 'required|string|max:255',
        ];
    }
}
