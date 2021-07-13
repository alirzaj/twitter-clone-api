<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\TweetVisited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementTweetImpressionsCount
{
    /**
     * Handle the event.
     *
     * @param  TweetVisited  $event
     * @return void
     */
    public function handle(TweetVisited $event)
    {
        $event->tweet->increment('impressions_count');
    }
}
