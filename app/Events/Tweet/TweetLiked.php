<?php

namespace App\Events\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class TweetLiked
{
    use Dispatchable;

    /**
     * TweetLiked constructor.
     * @param Tweet $tweet
     * @param User $user
     */
    public function __construct(public Tweet $tweet, public User $user)
    {
    }
}
