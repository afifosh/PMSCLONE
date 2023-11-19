<?php

namespace App\Http\Requests\Admin\Medium;

use App\Models\Company;
use App\Models\PartnerCompany;
use Illuminate\Foundation\Http\FormRequest;

class MediumStoreRequest extends FormRequest
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
      'name' => [
        'required',
        'string',
        'max:255',
        'unique:mediums,name', // Add the unique rule here
    ],
    ];

    if($this->method() == 'POST'){
      $rules['added_by'] = 'required';
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
      'name.required' => 'Medium name is required',
      'name.string' => 'Medium name must be a string',
      'name.max' => 'Medium name should not be greater than 255 characters',
    ];
  }
}
