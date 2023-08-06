<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.2.2
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MessageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'to' => 'bail|required|array',
            'cc' => 'bail|nullable|array',
            'bcc' => 'bail|nullable|array',
            // If changing the validation for recipients
            // check the front-end too
            'to.*.address' => 'email',
            'cc.*.address' => 'email',
            'bcc.*.address' => 'email',
            'subject' => 'required|string|max:191',
            'via_resource' => Rule::requiredIf($this->filled('task_date')),
            'via_resource_id' => Rule::requiredIf($this->filled('task_date')),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'to.*.address' => 'email address',
        ];
    }
}
