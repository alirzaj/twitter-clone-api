<?php

namespace App\Http\Requests\Authentication;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->numbers()
                    ->mixedCase()
                    ->uncompromised()
            ],
            'name' => ['required', 'string', 'max:70'],
            'username' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z_0-9.]{1,100}$/',
                Rule::unique('users', 'username')
            ]
        ];
    }
}
