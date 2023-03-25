<?php

namespace App\Actions\Fortify;

use Imanghafoori\PasswordHistory\Rules\NotBeInPasswordHistory;
use Laravel\Fortify\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function passwordRules(): array
    {
        $rules = ['required', 'string', new Password, 'confirmed'];

        if (! is_null(auth()->user())) {
            $rules[] = NotBeInPasswordHistory::ofUser(auth()->user());
        }

        return $rules;
    }
}
