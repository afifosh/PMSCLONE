<?php

namespace App\Http\Requests\Admin\Location;

use App\Models\Company;
use App\Models\PartnerCompany;
use Illuminate\Foundation\Http\FormRequest;

class LocationStoreRequest extends FormRequest
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
    $owner_types = ['Company' => Company::class, 'Client' => Company::class, 'PartnerCompany' => PartnerCompany::class];
    $this->merge([
      'is_public' => $this->boolean('is_public'),
      'is_warehouse' => $this->boolean('is_warehouse'),
      'owner_type' =>  isset($owner_types[$this->owner_type]) ? $owner_types[$this->owner_type] : null,
    ]);

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
      'name' => 'required|string|max:255',
      'country_id' => 'required|exists:countries,id',
      'state_id' => 'required|exists:states,id',
      'city_id' => 'required|exists:cities,id',
      'address' => 'required|string|max:255',
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',
      'zoomLevel' => 'required|numeric',
      'is_public' => 'required|boolean',
      'is_warehouse' => 'required|boolean',
      'owner_type' => 'required',
      'owner_id' => 'required|exists:' . ($this->owner_type == Company::class ? 'companies,id' : 'partner_companies,id'),
      'status' => 'required|in:Active,Inactive'
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
      'name.required' => 'Location name is required',
      'name.string' => 'Location name must be a string',
      'name.max' => 'Location name should not be greater than 255 characters',
      'country_id.required' => 'Country is required',
      'country_id.exists' => 'Country does not exist',
      'state_id.required' => 'State is required',
      'state_id.exists' => 'State does not exist',
      'city_id.required' => 'City is required',
      'city_id.exists' => 'City does not exist',
      'owner_type.required' => 'Owner type is required',
      'owner_type.string' => 'Owner type must be a string',
      'owner_id.required' => 'Owner is required',
    ];
  }
}
