<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class NotFromPasswordHistory implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $passwords = request()->user()->passwordHistories()->get();

        $passes = true;

        $passwords->each(function ($item) use (&$passes) {
            if (Hash::check(request()->password, $item->password)) {
                $passes = false;
                return $passes;
            }
        });

        return $passes;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('Password cannot be same as previous passwords');
    }
}
