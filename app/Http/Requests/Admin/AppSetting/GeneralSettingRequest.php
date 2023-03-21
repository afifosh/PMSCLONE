<?php

namespace App\Http\Requests\Admin\AppSetting;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'password_expire_days' => 'required|numeric|gt:1',
            'timeout_warning_seconds' => 'required|numeric',
            'timeout_after_seconds' => 'required|numeric|gte:3',
        ];
    }
}
