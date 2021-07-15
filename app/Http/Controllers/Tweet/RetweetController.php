<?php

namespace App\Http\Controllers\Tweet;

use App\Events\Tweet\Retweeted;
use App\Http\Controllers\Controller;
use App\Models\Tweet;

class RetweetController extends Controller
{
    public function store(int $tweet)
    {
        $tweet = Tweet::query()->findOrFail($tweet, ['id']);

        abort_if(
            $tweet->retweets()->where('users.id', auth()->id())->exists(),
            422,
            'already retweeted'
        );

        $tweet->retweets()->attach(auth()->user());

        Retweeted::dispatch($tweet, auth()->user());

        return response()->json([], 201);
    }
}
