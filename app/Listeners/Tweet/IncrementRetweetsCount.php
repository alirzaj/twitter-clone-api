<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\Retweeted;

class IncrementRetweetsCount
{
    /**
     * Handle the event.
     *
     * @param Retweeted $event
     * @return void
     */
    public function handle(Retweeted $event)
    {
        $event->tweet->increment('retweets_count');
    }
}
