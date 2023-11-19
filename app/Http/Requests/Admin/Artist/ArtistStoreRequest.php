<?php

namespace App\Http\Requests\Admin\Artist;

use Illuminate\Validation\Rule; // Add this line
use Illuminate\Foundation\Http\FormRequest;

class ArtistStoreRequest extends FormRequest
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
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',     
        // Unique validation for first_name and last_name
        'first_name' => [
          'required',
          'max:255',
          Rule::unique('artists')->where(function ($query) {
              return $query->where('first_name', $this->input('first_name'))
                           ->where('last_name', $this->input('last_name'));
          })->ignore($this->id), // Use 'ignore' to exclude the current record when updating
        ],
        'website' => ['nullable', 'string', 'max:255', 'unique:artists,website'],
        'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'email' => ['nullable', 'email', 'max:255', 'unique:artists,email'],
        'birth_date' => ['nullable','date','before_or_equal:today'],
        'death_date' => ['nullable','date','after:birth_date','before_or_equal:today'],
        'phone' => 'nullable|phone',
        'phone_country' => 'required_with:phone',
        'gender' => 'nullable|in:Male,Female,Other',
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

    // Add the artist's ID to the unique rule for update operations
    if ($this->method() == 'PUT' || $this->method() == 'PATCH') {
      $rules['email'] = [
          'required',
          'email',
          'max:255',
          Rule::unique('artists')->ignore($this->artist->id)
      ];

      $rules['first_name'] = [
        'required',
        'max:255',
        Rule::unique('artists')->where(function ($query) {
            return $query->where('first_name', $this->input('first_name'))
                         ->where('last_name', $this->input('last_name'));
        })->ignore($this->artist->id), // Use 'ignore' to exclude the current record when updating
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
      'first_name.required' => 'Artist first name is required',
      'first_name.string' => 'Artist first name must be a string',
      'first_name.max' => 'Artist first name should not be greater than 255 characters',
      'first_name.unique' => 'The combination of first name and last name already exists.',
      'last_name.required' => 'Artist last name is required',
      'last_name.string' => 'Artist last name must be a string',
      'last_name.max' => 'Artist last name should not be greater than 255 characters',      
      'country_id.required' => 'Country is required',
      'avatar.mimetypes' => 'Artist image must be an image file.',
      'phone.phone' => 'Phone number is invalid.',
      'phone_country.required_with' => 'Phone number is invalid.',
      'country_id.required' => 'Country is required.',
      'country_id.exists' => 'Country does not exist',
      'state_id.required' => 'State is required',
      'state_id.exists' => 'State does not exist',
      'city_id.required' => 'City is required',
      'city_id.exists' => 'City does not exist',
      'birth_date.before_or_equal' => 'The birth date must not be a future date.',
      'death_date.after' => 'The death date must be a date after the birth date.',
      'death_date.before_or_equal' => 'The death date must not be a future date.'
    ];
  }
}
