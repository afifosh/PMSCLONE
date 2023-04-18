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

use ResourceBundle;
use Illuminate\Contracts\Validation\Rule;

class Locale implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! extension_loaded('intl')) {
            return (bool) preg_match('/^[A-Za-z_]+$/', $value);
        }

        return in_array($value, ResourceBundle::getLocales(''));
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid locale, locale name should be in format: "de" or "de_DE" or "pt_BR"';
    }
}
