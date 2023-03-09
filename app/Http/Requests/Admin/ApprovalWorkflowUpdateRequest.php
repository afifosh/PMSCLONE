<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalWorkflowUpdateRequest extends FormRequest
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
          'workflow_name' => 'required|string',
          'level.*.name' => 'required|string',
          'level.*.approvers' => 'required|array',
          'level.*.approvers.*' => 'required||exists:admins,id',
        ];
    }

    public function messages()
    {
      return [
        'workflow_name.required' => 'Workflow Name is required',
        'level.*.name.required' => 'Level Name is required',
        'level.*.approvers.required' => 'Approvers are required',
        'level.*.approvers.*.required' => 'Approvers are required',
      ];
    }
}
