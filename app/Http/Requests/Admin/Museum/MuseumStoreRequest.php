<?php

namespace App\Http\Requests\Admin\Museum;

use Illuminate\Validation\Rule; // Add this line
use Illuminate\Foundation\Http\FormRequest;

class MuseumStoreRequest extends FormRequest
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
        'name' => ['required', 'string', 'max:255', 'unique:museums,name'],
        'website' => ['nullable', 'string', 'max:255', 'unique:museums,website'],
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'email' => ['nullable', 'email', 'max:255', 'unique:museums,email'],
        'phone' => 'nullable|phone',
        'phone_country' => 'required_with:phone',
        'address' => 'nullable|string|max:255',
        'city_id' => 'nullable|exists:cities,id',
        'state_id' => 'nullable|exists:states,id',
        'zip' => 'nullable|string|max:255',
        'country_id' => 'nullable|exists:countries,id',
        'language' => 'nullable|string|max:255',
        'timezone' => 'nullable|string|max:255',
        'currency' => 'nullable|string|max:255',
        'status' => 'required|in:Active,Inactive'
    ];

    if($this->method() == 'POST'){
      $rules['added_by'] = 'required';
    }

    // Add the museum's ID to the unique rule for update operations
    if ($this->method() == 'PUT' || $this->method() == 'PATCH') {
      $museumId = $this->museum->id ?? null;
      $rules['name'] = [
          'required',
          'string',
          'max:255',
          Rule::unique('museums')->ignore($museumId)
      ];
    }
  

    return $rules;
  }


  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array<string, string>
   */
  public function messages(): array
  {
    return [
      'name.required' => 'Museum name is required',
      'name.string' => 'Museum name must be a string',
      'name.max' => 'Museum name should not be greater than 255 characters',
      'country_id.required' => 'Country is required',
      'avatar.mimetypes' => 'Museum image must be an image file.',
      'phone.phone' => 'Phone number is invalid.',
      'phone_country.required_with' => 'Phone number is invalid.',
      'country_id.required' => 'Country is required.',
      'country_id.exists' => 'Country does not exist',
      'state_id.required' => 'State is required',
      'state_id.exists' => 'State does not exist',
      'city_id.required' => 'City is required',
      'city_id.exists' => 'City does not exist',
    ];
  }
}