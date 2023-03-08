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
          'name' => 'required|array',
          'name.*' => 'required|string',
          'approvers' => 'required|array',
          'approvers.*' => 'required|array',
          'approvers.*.*' => 'required|string|exists:admins,id',
        ];
    }

    public function messages()
    {
      return [
        'workflow_name.required' => 'Workflow Name is required',
        'name.*.required' => 'Level Name is required',
        'approvers.required' => 'Approvers are required',
        'approvers.*.*.required' => 'Approvers are required',
      ];
    }
}
