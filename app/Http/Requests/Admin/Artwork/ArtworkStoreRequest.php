<?php

namespace App\Http\Requests\Admin\Artwork;

use Illuminate\Foundation\Http\FormRequest;

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
    $this->merge([
      'year' => $this->program_id ? null : $this->year,
      'is_temporary_location' => $this->boolean('is_temporary_location'),
      'added_till' => $this->boolean('is_temporary_location') ? $this->added_till : null,
    ]);

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
      'title' => ['required', 'string', 'max:255'],
      'medium_id' => 'required|exists:mediums,id',
      'program_id' => ['nullable', 'required_without:year', 'integer', 'exists:programs,id'],
      'year' => ['nullable', 'required_without:program_id', 'digits:4'],
      'dimension' => ['required', 'string', 'max:255'],
      'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
      'location_id' => ['required', 'integer', 'exists:locations,id'],
      'description' => ['nullable', 'string', 'max:255'],
      'location_id' => ['required', 'integer', 'exists:locations,id'],
      'contract_id' => ['nullable', 'integer', 'exists:contracts,id'],
      'is_temporary_location' => ['boolean'],
      'added_till' => ['nullable', 'required_if:is_temporary_location,true', 'date'],
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
      'year.required_without' => 'Year is required',
      'dimension.required' => 'Dimension is required',
      'featured_image.image' => 'Featured image must be an image',
      'featured_image.mimes' => 'Featured image must be a file of type: jpeg, png, jpg, gif, svg.',
      'featured_image.max' => 'Featured image must be of size less than 2 MB',
      'location_id.*' => 'Location is required',
    ];
  }
}
