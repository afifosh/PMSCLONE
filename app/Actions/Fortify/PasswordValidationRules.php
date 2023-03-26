<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Models\User;
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

        $user = $this->getUser();

        if ($user) {
            $rules[] = NotBeInPasswordHistory::ofUser($user);
        }

        return $rules;
    }

    private function getUser()
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
