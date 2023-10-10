<?php

namespace App\Http\Requests\Admin\Contract;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeRequestStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
   */
  public function rules(): array
  {
    $rules = [
      'reason' => 'required|string',
      'description' => 'nullable|string',
      'action_type' => 'required|string|in:update-terms,pause-contract,resume-contract,terminate-contract'
    ];

    if ($this->action_type == 'update-terms') {
      $rules = $rules + [
        'value_action' => 'required|string|in:inc,dec,unchanged',
        'value_change' => 'nullable|required_if:value_action,inc,dec|numeric',
        'currency' => ['nullable', 'required', 'string', Rule::in(array_keys(config('money.currencies')))],
        'timeline_action' => 'required|string|in:inc,dec,unchanged',
        'new_end_date' => 'nullable|required_if:timeline_action,inc,dec|date|after:' . $this->contract->start_date,
      ];
    } elseif ($this->action_type == 'pause-contract') {
      $rules = $rules + [
        'pause_until' => 'required|in:manual,custom_date,custom_unit,custom_date_from',
        'custom_date_value' => 'nullable|required_if:pause_until,custom_date|date|after:today',
        'custom_unit' => 'required_if:pause_until,custom_unit|in:Days,Weeks,Months',
        'pause_for' => 'nullable|required_if:pause_until,custom_unit|numeric|min:1',
        'custom_from_date_value' => 'nullable|required_if:pause_until,custom_date_from|date|after: ' . $this->contract->start_date,
      ];
    } elseif ($this->action_type == 'terminate-contract') {
      $rules = $rules + [
        'terminate_date' => 'nullable|in:now,custom',
        'custom_date' => 'nullable|required_if:terminate_date,custom|date',
      ];
    } elseif ($this->action_type == 'resume-contract') {
      $rules = $rules + [
        'resume_date' => 'nullable|in:now,custom',
        'custom_resume_date' => 'nullable|required_if:resume_date,custom|date',
      ];
    }

    return $rules;
  }

  public function messages()
  {
    return [
      '*.required_if' => __('This field is required')
    ];
  }
}
