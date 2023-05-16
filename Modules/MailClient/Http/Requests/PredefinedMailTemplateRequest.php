<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.9
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace Modules\MailClient\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Core\Rules\UniqueRule;
use Modules\MailClient\Models\PredefinedMailTemplate;

class PredefinedMailTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                UniqueRule::make(PredefinedMailTemplate::class, 'template'),
                'max:191',
            ],
            'subject' => 'required|string|max:191',
            'body' => 'required|string',
            'is_shared' => 'required|boolean',
        ];
    }
}
