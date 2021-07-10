<?php

namespace App\Http\Controllers\Tweet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tweet\StoreTweetRequest;

class TweetController extends Controller
{
    public function store(StoreTweetRequest $request)
    {
        auth()->user()->tweets()->create($request->validated());

        return response()->json([], 201);
    }
}
