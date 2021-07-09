<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Query\JoinClause;

class UserRepository
{
    /**
     * @param User $user
     * @return CursorPaginator
     */
    public function followers(User $user): CursorPaginator
    {
        return User::query()
            ->select(['users.id', 'users.name', 'users.username', 'users.bio'])
            ->with(['media' => function ($query) {
                $query->where('collection_name', 'avatar');
            }])
            ->withFollowingState(auth()->user())
            ->withFollowState(auth()->user())
            ->join('follows', function (JoinClause $join) {
                $join->on('follows.follower_id', '=', 'users.id');
            })
            ->where('follows.following_id', $user->id)
            ->cursorPaginate();
    }

    /**
     * @param User $user
     * @return CursorPaginator
     */
    public function followings(User $user): CursorPaginator
    {
        return User::query()
            ->select(['users.id', 'users.name', 'users.username', 'users.bio'])
            ->with(['media' => function ($query) {
                $query->where('collection_name', 'avatar');
            }])
            ->withFollowingState(auth()->user())
            ->withFollowState(auth()->user())
            ->join('follows', function (JoinClause $join) {
                $join->on('follows.following_id', '=', 'users.id');
            })
            ->where('follows.follower_id', $user->id)
            ->cursorPaginate();
    }
}
