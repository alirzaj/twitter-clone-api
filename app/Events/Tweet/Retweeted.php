<?php

namespace App\Events\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class Retweeted
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(public Tweet $tweet, public User $user)
    {
    }
}
