<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
      'name' => 'required|string|max:255|unique:projects,name,' . $this->route('project')->id . ',id',
      'company_id' => 'nullable|exists:companies,id',
      'program_id' => 'required|exists:programs,id',
      'category_id' => 'required|exists:project_categories,id',
      'status' => 'required',
      'members' => 'required|array',
      'members.*' => 'required|exists:admins,id',
      'description' => 'nullable|string|max:2000',
      'start_date' => 'nullable|date',
      'deadline' => 'nullable|date',
      // 'is_progress_calculatable' => 'required',
      'tags' => 'nullable|array',
      'refrence_id' => 'nullable|string|max:255',
      'budget' => 'nullable|numeric|min:0',
      // 'progress' => 'required_if:is_progress_calculateable,0|numeric|min:0|max:100',
    ];
  }

  public function messages()
  {
    return [
      'company_id.required' => 'Please select company',
      'company_id.exists' => 'Please select valid company',
      'members.required' => 'Please select at least one member',
      'members.*.required' => 'Please select at least one member',
      'members.*.exists' => 'Please select valid member',
      'program_id.required' => 'Please select program',
      'program_id.exists' => 'Please select valid program',
      'category_id.required' => 'Please select category',
      'category_id.exists' => 'Please select valid category',
    ];
  }
}
