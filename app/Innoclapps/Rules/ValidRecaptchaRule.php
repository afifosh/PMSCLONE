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

namespace App\Innoclapps\Rules;

use Illuminate\Support\Facades\Http;
use App\Innoclapps\Facades\ReCaptcha;
use Illuminate\Contracts\Validation\Rule;

class ValidRecaptchaRule implements Rule
{
    /**
     * @var string
     */
    protected $verifyEndpoint = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        return Http::asForm()->post($this->verifyEndpoint, [
            'secret'   => ReCaptcha::getSecretKey(),
            'response' => $value,
        ])['success'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.recaptcha');
    }
}
