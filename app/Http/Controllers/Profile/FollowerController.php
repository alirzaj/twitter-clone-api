<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OtherUserFollowersResource;
use App\Models\User;
use Illuminate\Database\Query\JoinClause;

class FollowerController extends Controller
{
    public function index(string $user)
    {
        $user = User::query()->select('id')->where('username', $user)->firstOrFail();

        $followers = User::query()
            ->select('users.*')
            ->selectRaw(
                '(select exists( select * from follows where following_id = users.id AND follower_id = ?)) as following',
                [auth()->id()]
            )
            ->selectRaw(
                '(select exists( select * from follows where following_id = ? AND follower_id = users.id)) as follows',
                [auth()->id()]
            )
            ->join('follows', function (JoinClause $join) {
                $join->on('follows.follower_id', '=', 'users.id');
            })
            ->where('follows.following_id', $user->id)
            ->cursorPaginate();

        return OtherUserFollowersResource::collection($followers);
    }
}
