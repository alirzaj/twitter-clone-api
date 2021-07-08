<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;

class FollowController extends Controller
{
    public function store(string $user)
    {
        $user = User::query()->select('id')->where('username', $user)->firstOrFail();

        abort_if(
            auth()->user()->followings()->where('id', $user->id)->exists(),
            422,
            'already followed'
        );

        $user->followers()->attach(auth()->user());

        return response()->json([], 204);
    }
}
