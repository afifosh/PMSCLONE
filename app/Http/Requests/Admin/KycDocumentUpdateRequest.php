<?php

namespace App\Http\Requests\Admin;

use App\Models\KycDocument;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KycDocumentUpdateRequest extends FormRequest
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
          'title' => ['required', 'string'],
          'required_from' => ['required'],
          'status' => ['required', 'boolean'],
          'fields' => ['required', 'array'],
          'fields.*.is_required' => ['required', 'boolean'],
          'fields.*.label' => ['required', 'string'],
          'fields.*.type' => ['required', 'string', Rule::in(KycDocument::TYPES)],
        ];
    }

    public function messages()
    {
        return [
          'fields.*.label.required' => __('Label is required.'),
        ];
    }
}
