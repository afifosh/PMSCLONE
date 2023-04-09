<?php

namespace App\Http\Requests\Admin\CompanyProfile;

use Illuminate\Foundation\Http\FormRequest;

class ApprovalUpdateRequest extends FormRequest
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
            'modification_ids' => 'required|array',
            'modification_ids.*' => 'required|exists:modifications,id',
            'approval_status' => 'required|array',
            'approval_status.*' => 'nullable',
            'comment' => 'array',
        ];
    }
}
