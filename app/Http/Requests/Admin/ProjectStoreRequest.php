<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'program_id' => 'required|exists:programs,id',
            'category_id' => 'required|exists:project_categories,id',
            'status' => 'required',
            'members' => 'required|array',
            'members.*' => 'required|exists:admins,id',
            'description' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'deadline' => 'required|date|after_or_equal:start_date',
            'is_progress_calculatable' => 'required',
            'tags' => 'nullable|array',
            // 'progress' => 'required_if:is_progress_calculateable,0|numeric|min:0|max:100',
        ];
    }

    public function messages()
    {
        return [
            'members.required' => 'Please select at least one member',
            'members.*.exists' => 'Please select valid member',
            'program_id.required' => 'Please select program',
            'program_id.exists' => 'Please select valid program',
            'category_id.required' => 'Please select category',
            'category_id.exists' => 'Please select valid category',
        ];
    }
}
