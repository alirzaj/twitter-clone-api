<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;

class UserController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json([], 204);
    }
}
