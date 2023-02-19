<?php

namespace App\Http\Requests\Auth;

use App\Rules\NotFromPasswordHistory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordExpiredRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required',
            'password' => [
                'required', 'confirmed', new NotFromPasswordHistory,
                Password::min(6)->letters()->numbers()->mixedCase()->uncompromised(),
            ],
        ];
    }
}
