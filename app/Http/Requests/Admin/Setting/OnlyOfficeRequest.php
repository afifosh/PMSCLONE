<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class OnlyOfficeRequest extends FormRequest
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
            'secret' => 'required|string|max:255',
            'doc_server_url' => 'required|url',
            'doc_server_api_url' => 'required|url',
            'supported_files' => 'required|string|max:255',
            'allowed_file_size' => [
                'required',
                'numeric',
                'size:200',
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'allowed_file_size.size' => 'The allowed file size may not be greater than :size MB.',
        ];
    }

}


