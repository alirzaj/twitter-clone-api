<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\TweetLiked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementLikesCount
{
    /**
     * Handle the event.
     *
     * @param  TweetLiked  $event
     * @return void
     */
    public function handle(TweetLiked $event)
    {
        $event->tweet->increment('likes_count');
    }
}
