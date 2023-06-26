<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\User;
use App\Rules\ForbiddenPasswordRule;
use Illuminate\Validation\Rules\Password;
use Imanghafoori\PasswordHistory\Rules\NotBeInPasswordHistory;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function passwordRules(): array
    {
        $rules = [
          'required',
          'confirmed',
          Password::min(8)->mixedCase()->uncompromised()->letters()->numbers()->symbols()
        ];

        $user = $this->guessUser();

        if ($user) {
            $rules[] = NotBeInPasswordHistory::ofUser($user);
            $rules[] = new ForbiddenPasswordRule($user);
        }

        return $rules;
    }

    private function guessUser()
    {
        if (! is_null(auth()->user())) {
            return auth()->user();
        }

        $request = app('request');
        $route = $request->route();

        if(! $request->has('email')) {
            return false;
        }

        if($route->named('admin.password.update')) {
            $model = Admin::class;
        } else if($route->named('password.update')) {
            $model = User::class;
        }

        return $model::query()->where('email', $request->email)->first();
    }
}
