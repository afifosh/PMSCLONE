<?php

namespace App\Http\Requests\Admin\Artwork;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\LengthUnit;
use App\Enums\WeightUnit;

class ArtworkStoreRequest extends FormRequest
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

    if($this->method() == 'POST')
      $this->merge([
        'added_by' => auth()->id()
      ]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {


    $rules = [
      'title' => 'required|string|max:255', // Assuming title is required and has a maximum length
      'owner_type' => 'nullable|string|max:255', // For the morphs 'owner' (if using in form)
      'owner_id' => 'nullable|integer|exists:owners,id', // Replace 'owners' with actual table name if needed
      'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'), // Year must be a valid year, change range if needed
      'medium_id' => 'required|exists:mediums,id', // Check if medium_id exists in mediums table
      'program_id' => 'required|exists:programs,id', // Check if program_id exists in programs table
      'description' => 'nullable|string', // Assuming any string is acceptable for description
      'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // This rule validates the file type and size.
      'weight' => 'required|numeric|min:0|max:999999.99', // Adjust the range if needed
      'weight_unit' => 'required|in:' . implode(',', array_values(WeightUnit::asSelectArray())),
      'width' => 'required|numeric|min:0|max:999999.99',
      'width_unit' => 'required|in:' . implode(',', array_values(LengthUnit::asSelectArray())),
      'height' => 'required|numeric|min:0|max:999999.99',
      'height_unit' => 'required|in:' . implode(',', array_values(LengthUnit::asSelectArray())),
      'depth' => 'required|numeric|min:0|max:999999.99',
      'depth_unit' => 'required|in:' . implode(',', array_values(LengthUnit::asSelectArray())),
      'added_by' => 'nullable|exists:admins,id', // Check if added_by exists in admins table
  ];    

    if($this->method() == 'POST')
      $rules['added_by'] = 'required';

    return $rules;
  }

  public function messages(): array
  {
    return [
      'title.required' => 'Title is required',
      'medium_id.required' => 'Medium is required',
      'medium_id.exists' => 'Medium is invalid',
      'program_id.required_without' => 'Program is required',
      'program_id.exists' => 'Program is invalid',
      'featured_image.image' => 'Featured image must be an image',
      'featured_image.mimes' => 'Featured image must be a file of type: jpeg, png, jpg, gif, svg.',
      'featured_image.max' => 'Featured image must be of size less than 2 MB',
      'weight_unit.in' => 'The selected weight unit is invalid. Allowed options are: ' . implode(', ', array_values(WeightUnit::asSelectArray())),
      'width_unit.in' => 'The selected width unit is invalid. Allowed options are: ' . implode(', ', array_values(LengthUnit::asSelectArray())),
      'height_unit.in' => 'The selected height unit is invalid. Allowed options are: ' . implode(', ', array_values(LengthUnit::asSelectArray())),
      'depth_unit.in' => 'The selected depth unit is invalid. Allowed options are: ' . implode(', ', array_values(LengthUnit::asSelectArray())),
   
    ];
  }
}
