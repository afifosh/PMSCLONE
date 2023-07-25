<?php

namespace Modules\Chat\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Chat\Models\ZoomMeeting;

class CreateMeetingRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return ZoomMeeting::$rules;
    }

    /**
     * @return string[]
     */
    public function messages()
    {
        return [
            'agenda.required' => 'Description field is required.',
        ];
    }
}
