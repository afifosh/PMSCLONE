<?php
/**
 * Concord CRM - https://www.concordcrm.com
 *
 * @version   1.1.6
 *
 * @link      Releases - https://www.concordcrm.com/releases
 * @link      Terms Of Service - https://www.concordcrm.com/terms
 *
 * @copyright Copyright (c) 2022-2023 KONKORD DIGITAL
 */

namespace App\Http\Requests;

use App\Models\PredefinedMailTemplate;
use App\Innoclapps\Rules\UniqueRule;
use Illuminate\Foundation\Http\FormRequest;

class PredefinedMailTemplateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                UniqueRule::make(PredefinedMailTemplate::class, 'template'),
                'max:191',
            ],
            'subject'   => 'required|string|max:191',
            'body'      => 'required|string',
            'is_shared' => 'required|boolean',
        ];
    }
}
