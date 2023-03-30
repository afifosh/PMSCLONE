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

class MustBeFilledInArrayRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected string $key, protected string $message)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param array $value
     * @return boolean
     */
    public function passes($attribute, $value)
    {
        return count(array_filter($value, function ($var) {
            return ($var && isset($var[$this->key]));
        })) === count($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
