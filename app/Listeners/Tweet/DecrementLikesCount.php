<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\TweetLikeRevoked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DecrementLikesCount
{
    /**
     * Handle the event.
     *
     * @param  TweetLikeRevoked  $event
     * @return void
     */
    public function handle(TweetLikeRevoked $event)
    {
        $event->tweet->decrement('likes_count');
    }
}
