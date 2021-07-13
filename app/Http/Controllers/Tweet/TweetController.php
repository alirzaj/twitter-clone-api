<?php

namespace App\Http\Controllers\Tweet;

use App\Events\Tweet\TweetVisited;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tweet\StoreTweetRequest;
use App\Http\Resources\Tweet\ShowTweetResource;
use App\Models\Tweet;
use Illuminate\Http\Request;

class TweetController extends Controller
{
    public function store(StoreTweetRequest $request)
    {
        auth()->user()->tweets()->create($request->validated());

        return response()->json([], 201);
    }

    public function show(Request $request, Tweet $tweet)
    {
        TweetVisited::dispatchUnless(
            $tweet->impressions()->where('user_id', auth()->id())->exists(),
            $tweet,
            auth()->user(),
            $request->ip(),
            $request->userAgent()
        );

        return new ShowTweetResource($tweet->load('user:id,name,username'));
    }
}
