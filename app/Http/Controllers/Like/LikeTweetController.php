<?php

namespace App\Http\Controllers\Like;

use App\Events\Tweet\TweetLiked;
use App\Events\Tweet\TweetLikeRevoked;
use App\Http\Controllers\Controller;
use App\Models\Tweet;

class LikeTweetController extends Controller
{
    public function store(int $tweet)
    {
        $tweet = Tweet::query()->findOrFail($tweet, ['id']);

        abort_if(
            $tweet->likes()->where('users.id', auth()->id())->exists(),
            401,
            'already liked'
        );

        $tweet->likes()->attach(auth()->user());

        TweetLiked::dispatch($tweet, auth()->user());

        return response()->json([], 201);
    }

    public function destroy(int $tweet)
    {
        $tweet = Tweet::query()->findOrFail($tweet, ['id']);

        abort_unless(
            $tweet->likes()->where('users.id', auth()->id())->exists(),
            401,
            'not liked'
        );

        $tweet->likes()->detach(auth()->user());

        TweetLikeRevoked::dispatch($tweet, auth()->user());

        return response()->json([], 201);
    }
}
