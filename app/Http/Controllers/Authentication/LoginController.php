<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Resources\Authentication\LoginResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * attempt to login a user & return a token if credentials where true
     *
     * @param LoginRequest $request
     * @return LoginResource
     */
    public function store(LoginRequest $request)
    {
        $user = User::query()->firstWhere('email', $request->input('email'));

        abort_if(
            is_null($user) || !Hash::check($request->input('password'), $user->password),
            401,
            'incorrect credentials'
        );

        return new LoginResource($user);
    }
}
