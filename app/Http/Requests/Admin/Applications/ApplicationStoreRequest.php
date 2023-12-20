<?php

namespace App\Http\Requests\Admin\Applications;

use Illuminate\Foundation\Http\FormRequest;

class ApplicationStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  public function prepareForValidation(): void
  {
    $this->merge([
      'is_public' => $this->boolean('is_public'),
      'company_id' => $this->boolean('is_public') ? null : $this->company_id,
    ]);
  }
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    return [
      'name' => 'required|string|max:255',
      'description' => 'nullable|string|max:255',
      'program_id' => 'required|exists:programs,id',
      'type_id' => 'required|exists:application_types,id',
      'category_id' => 'required|exists:application_categories,id',
      'pipeline_id' => 'required|exists:application_pipelines,id',
      'scorecard_id' => 'required|exists:application_score_cards,id',
      'is_public' => 'required|boolean',
      'company_id' => 'required_if:is_public,false|nullable|exists:companies,id',
      'form_id' => 'nullable',
      'start_at' => 'required|date',
      'end_at' => 'required|date',
      'application_users' => 'required|array',
      'application_users.*' => 'required|exists:admins,id',
    ];
  }

  public function messages()
  {
    return [
      'program_id.required' => 'The program field is required.',
      'type_id.required' => 'The type field is required.',
      'category_id.required' => 'The category field is required.',
      'pipeline_id.required' => 'The pipeline field is required.',
      'scorecard_id.required' => 'The scorecard field is required.',
      'company_id.required_if' => 'The company field is required.',
    ];
  }
}
