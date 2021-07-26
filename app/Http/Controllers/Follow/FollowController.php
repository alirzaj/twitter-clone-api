<?php

namespace App\Http\Controllers\Follow;

use App\Events\User\UserFollowed;
use App\Events\User\UserUnfollowed;
use App\Http\Controllers\Controller;
use App\Models\User;

class FollowController extends Controller
{
    public function store(string $user)
    {
        $following = User::query()
            ->select('id')
            ->where('username', $user)
            ->firstOrFail();

        abort_if(
            auth()->user()->followings()->where('id', $following->id)->exists() ||
            $following->is(auth()->user()),
            422,
            'already followed or following yourself'
        );

        $following->followers()->attach(auth()->user());

        UserFollowed::dispatch(auth()->user(), $following);

        return response()->json([], 204);
    }

    public function destroy(string $user)
    {
        $userToUnfollow = User::query()
            ->select('id')
            ->where('username', $user)
            ->firstOrFail();

        abort_unless(
            auth()->user()->followings()->where('id', $userToUnfollow->id)->exists(),
            422,
            'not followed'
        );

        $userToUnfollow->followers()->detach(auth()->user());

        UserUnfollowed::dispatch(auth()->user(), $userToUnfollow);

        return response()->json([], 204);
    }
}
