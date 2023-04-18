<?php


namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class MessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'to'  => 'bail|required',
            'cc'  => 'bail|nullable|array',
            'bcc' => 'bail|nullable|array',
            // If changing the validation for recipients
            // check the front-end too
            'to.*.value'    => 'email',
            'cc.*.value'    => 'email',
            'bcc.*.value'   => 'email',
            'subject'         => 'required|string|max:191',
            'via_resource'    => Rule::requiredIf($this->filled('task_date')),
            'via_resource_id' => Rule::requiredIf($this->filled('task_date')),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'to.*.value' => 'email address',
        ];
    }
}
