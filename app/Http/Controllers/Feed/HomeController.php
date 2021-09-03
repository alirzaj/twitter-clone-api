<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Http\Resources\Feed\HomeResource;
use App\Models\Tweet;
use Illuminate\Database\Query\Builder;

class HomeController extends Controller
{
    public function index()
    {
        //TODO user avatar n+1? eagerload?
        return HomeResource::collection(
            Tweet::query()
                ->with('user:id,name,username')
                ->withExpression(
                    'followings',
                    fn(Builder $query) => $query
                        ->select('following_id as user_id')
                        ->from('follows')
                        ->where('follower_id', auth()->id())
                )
                ->whereIn(
                    'user_id',
                    fn(Builder $query) => $query
                        ->select('user_id')
                        ->from('followings')
                )
                ->orWhereIn(
                    'id',
                    fn(Builder $query) => $query
                        ->select('tweet_id')
                        ->from('retweets')
                        ->whereIn(
                            'user_id',
                            fn(Builder $query) => $query
                                ->select('user_id')
                                ->from('followings')
                        )
                )
                ->latest()
                ->cursorPaginate()
        );
    }
}
