<?php

namespace App\Http\Requests\Admin\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SecurityRequest extends FormRequest
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
            'password_history_depth' => 'required|numeric|min:1|max:10',
            'password_expire_days' => 'required|numeric|gt:1',
            'timeout_warning_seconds' => 'required|numeric',
            'timeout_after_seconds' => 'required|numeric|gte:3|gt:timeout_warning_seconds',
        ];
    }

    public function messages()
    {
      return [
        'timeout_after_seconds.gt' => 'The timeout after must be greater than or equal to timeout warning.',
      ];
    }
}
