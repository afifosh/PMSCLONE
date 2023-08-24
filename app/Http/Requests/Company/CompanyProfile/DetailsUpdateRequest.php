<?php

namespace App\Http\Requests\Company\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DetailsUpdateRequest extends FormRequest
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
      'submit_type' => 'required|string',
      'name' => 'required_if:submit_type,submit|nullable|string|max:255',
      'logo' => Rule::requiredIf(function () {
        return $this->submit_type == 'submit' && !@auth()->user()->company->getPOCLogo();
      }),
      'website' => 'required_if:submit_type,submit|nullable|max:255',
      'locality_type' => 'required_if:submit_type,submit|nullable|integer',
      'geographical_coverage' => 'nullable|array',
      'date_founded' => 'required_if:submit_type,submit|nullable|date',
      'duns_number' => 'nullable|string|max:255',
      'no_of_employees' => 'required_if:submit_type,submit|nullable|string|max:255',
      'legal_form' => 'required_if:submit_type,submit|nullable|string|max:255',
      'description' => 'nullable|string|max:2000',
      'facebook_url' => 'nullable|url|max:255',
      'twitter_url' => 'nullable|url|max:255',
      'linkedin_url' => 'nullable|url|max:255',
      'youtube_url' => 'nullable|url|max:255',
      'is_sa_available' => 'nullable|string',
      'industries' => 'required|array',
      'industries.*' => 'required|integer',
      'sa_company_name' => Rule::requiredIf(function () {
        return $this->is_sa_available && $this->submit_type == 'submit';
      }),
      'is_subsidory' => 'string|nullable',
      'parent_company' => Rule::requiredIf(function () {
        return $this->is_subsidory && $this->submit_type == 'submit';
      }),
      'is_parent' => 'string|nullable',
      'subsidiaries' => Rule::requiredIf(function () {
        return $this->is_parent && $this->submit_type == 'submit';
      }),
    ];
  }

  public function messages()
  {
    return [
      '*.required_if' => 'This field is required',
    ];
  }
}
