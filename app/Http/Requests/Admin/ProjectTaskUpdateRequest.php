<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectTaskUpdateRequest extends FormRequest
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
        'subject' => ['required', 'string', 'max:255', Rule::unique('tasks')->where('subject', $this->subject)->where('project_id', request()->task->project_id)->ignore(request()->task)],
        'description' => 'nullable|string|max:255',
        'start_date' => 'nullable|date',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:Low,Medium,High,Urgent',
        'tags' => 'nullable|array',
        'status' => 'required|in:Not Started,In Progress,On Hold,Awaiting Feedback,Completed',
        'assignees' => 'required|array',
        'assignees.*' => 'required|exists:admins,id',
        'followers' => 'nullable|array',
        'followers.*' => 'nullable|exists:admins,id',
        // 'files' => 'nullable|array',
        // 'files.*' => 'nullable|file',
      ];
    }

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
