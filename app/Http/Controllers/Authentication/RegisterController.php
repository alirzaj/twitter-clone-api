<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Resources\Authentication\RegisterResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Create a new registered user.
     *
     * @param RegisterRequest $request
     * @return RegisterResource
     */
    public function store(RegisterRequest $request)
    {
        $user = User::query()->create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'name' => $request->input('name'),
            'username' => $request->input('username'),
        ]);

        event(new Registered($user));

        return new RegisterResource($user);
    }
}
