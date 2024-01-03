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
      'has_end_date' => $this->boolean('has_end_date') ?? false,
      'end_at' => $this->boolean('has_end_date') ? $this->end_at : null,

      'notification_emails' => $this->notification_emails ?? [],
      'notification_emails_cc' => $this->notification_emails_cc ?? [],
      'notification_emails_bcc' => $this->notification_emails_bcc ?? [],
      'allow_comments' => $this->boolean('allow_comments'),
      'allow_share_section' => $this->boolean('allow_share_section'),

      'application_users' => $this->application_users ?? [],

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
      // step 1
      'name' => 'required|string|max:255',
      'program_id' => 'required|exists:programs,id',
      'type_id' => 'required|exists:application_types,id',
      'category_id' => 'required|exists:application_categories,id',
      'pipeline_id' => 'required|exists:application_pipelines,id',
      'scorecard_id' => 'required|exists:application_score_cards,id',
      'start_at' => 'required|date',
      'has_end_date' => 'required|boolean',
      'end_at' => 'nullable|required_if:has_end_date,true|date|after_or_equal:start_at',
      'description' => 'nullable|string|max:255',
      // step 2
      'form_id' => 'required|exists:forms,id',
      'success_message' => 'required|string|max:255',
      'notification_emails' => 'required|array',
      'notification_emails.*' => 'required|email',
      'notification_emails_cc' => 'nullable|array',
      'notification_emails_cc.*' => 'nullable|email',
      'notification_emails_bcc' => 'nullable|array',
      'notification_emails_bcc.*' => 'nullable|email',
      'allow_comments' => 'required|boolean',
      'allow_share_section' => 'required|boolean',
      // step 3
      'application_users' => 'required|array',
      'application_users.*' => 'required|exists:admins,id',
      // step 4
      'is_public' => 'required|boolean',
      'company_ids' => 'required_if:is_public,false|nullable|exists:companies,id',
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
