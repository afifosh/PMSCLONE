<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectTaskStoreRequest extends FormRequest
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
    return [
      'subject' => 'required|string|max:255',
      'description' => 'nullable|string|max:255',
      'start_date' => 'required|date',
      'due_date' => 'required|date',
      'priority' => 'required|in:low,medium,high,urgent',
      'tags' => 'nullable|array',
      'status' => 'required|in:not started,in progress,on hold,awaiting feedback,completed',
      'assignees' => 'required|array',
      'assignees.*' => 'required|exists:admins,id',
      'followers' => 'nullable|array',
      'followers.*' => 'nullable|exists:admins,id',
      // 'files' => 'nullable|array',
      // 'files.*' => 'nullable|file',
    ];
  }

  /**
   * Get the error messages for the defined validation rules.
   *
   * @return array<string, string>
   */

  public function messages(): array
  {
    return [
      'assignees.required' => 'Please select at least one assignee.',
      'assignees.*.required' => 'Please select at least one assignee.',
      'assignees.*.exists' => 'The selected assignee is invalid.',
      'followers.*.exists' => 'The selected follower is invalid.',
    ];
  }
}
