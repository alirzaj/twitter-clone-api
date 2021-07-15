<?php

namespace App\Http\Controllers\Tweet;

use App\Events\Tweet\TweetReplied;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tweet\StoreTweetRequest;
use App\Models\Tweet;

class ReplyController extends Controller
{
    public function store(StoreTweetRequest $request, int $tweet)
    {
        $tweet = Tweet::query()->findOrFail($tweet, ['id']);

        $tweet->replies()->create($request->validated() + ['user_id' => auth()->id()]);

        TweetReplied::dispatch($tweet, auth()->user());

        return response()->json([], 201);
    }
}
