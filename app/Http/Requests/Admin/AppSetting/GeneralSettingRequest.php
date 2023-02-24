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
            'password_history_count' => 'required|numeric|min:1|max:10',
            'password_expire_days' => 'required|numeric|gt:1',
            'timeout_warning_seconds' => 'required|numeric|gte:3',
            'timeout_after_seconds' => 'required|numeric|gt:timeout_warning_seconds',
        ];
    }
}
