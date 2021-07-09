<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;

class UserController extends Controller
{
    public function update(UpdateProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return response()->json([], 204);
    }
}
