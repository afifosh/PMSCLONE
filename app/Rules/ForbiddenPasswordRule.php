<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ForbiddenPasswordRule implements Rule
{
    protected $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function passes($attribute, $value)
    {
        $forbiddenPasswords = [
            $this->user->first_name,
            $this->user->last_name,
            $this->user->email,
        ];

        foreach ($forbiddenPasswords as $password) {
            $passwordArr = explode(' ', $password);
            foreach ($passwordArr as $fPass) {
                if (strpos(strtolower($value), strtolower($fPass)) !== false){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Name or email cannot be part of password');
    }
}
