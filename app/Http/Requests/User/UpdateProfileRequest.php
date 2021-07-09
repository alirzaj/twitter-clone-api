<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore(auth()->user()->email)
            ],
            'name' => ['required', 'string', 'max:70'],
            'username' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z_0-9.]{1,100}$/',
                Rule::unique('users', 'username')->ignore(auth()->user()->username)
            ],
            'phone' => ['sometimes', 'string', 'regex:/^[1-9][0-9]{7,13}$/'],
            'birthday' => [
                'sometimes',
                'date_format:"Y-m-d"',
                'before:' . now()->subYears(12)->format('Y-m-d'),
                'after:1950-01-01'
            ],
            'bio' => ['sometimes', 'string', 'max:250'],
            'location' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
