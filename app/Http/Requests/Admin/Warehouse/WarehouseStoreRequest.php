<?php

namespace App\Http\Requests\Admin\Warehouse;

use App\Models\Company;
use App\Models\PartnerCompany;
use Illuminate\Foundation\Http\FormRequest;

class WarehouseStoreRequest extends FormRequest
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
      'name' => 'required|string|max:255|unique:warehouses,name',
      'country_id' => 'required|exists:countries,id',
      'state_id' => 'required|exists:states,id',
      'city_id' => 'required|exists:cities,id',
      'address' => 'required|string|max:255',
      'longitude' => 'required|numeric|between:-180,180',
      'latitude' => 'required|numeric|between:-90,90',
      'zoomLevel' => 'required|numeric|between:0,21',
      'owner_type' => 'required',
      'owner_id' => 'required|exists:' . ($this->owner_type == Company::class ? 'companies,id' : 'partner_companies,id'),
      'status' => 'required|in:Active,Inactive'
    ];

    if($this->method() == 'POST'){
      $rules['added_by'] = 'required';
    }

    // Add the warehouse's ID to the unique rule for update operations
    if ($this->method() == 'PUT' || $this->method() == 'PATCH') {
      // Assuming you have the warehouse's ID available
      $warehouseId = $this->warehouse->id ?? null;
      $rules['name'] .= ',' . $warehouseId;
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
      'name.required' => 'Warehouse name is required',
      'name.string' => 'Warehouse name must be a string',
      'name.max' => 'Warehouse name should not be greater than 255 characters',
      'country_id.required' => 'Country is required',
      'country_id.exists' => 'Country does not exist',
      'state_id.required' => 'State is required',
      'state_id.exists' => 'State does not exist',
      'city_id.required' => 'City is required',
      'city_id.exists' => 'City does not exist',
      'owner_type.required' => 'Owner type is required',
      'owner_type.string' => 'Owner type must be a string',
      'owner_id.required' => 'Owner is required',
      'zoomLevel.required' => 'Zoom level is required',
      'zoomLevel.numeric' => 'Zoom level must be a numeric value',
      'zoomLevel.between' => 'Zoom level must be between 0 and 21',
      'longitude.required' => 'The longitude is required.',
      'longitude.between' => 'The longitude must be between -180 and 180.',
      'latitude.required' => 'The latitude is required.',
      'latitude.between' => 'The latitude must be between -90 and 90.',
    ];
  }
}
