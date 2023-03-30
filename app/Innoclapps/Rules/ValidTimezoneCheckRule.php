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

use Illuminate\Contracts\Validation\Rule;

class ValidTimezoneCheckRule implements Rule
{
    /**
     * The rule checks if a passed timezone is valid timezone
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        return in_array($value, tz()->all());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.timezone');
    }
}
