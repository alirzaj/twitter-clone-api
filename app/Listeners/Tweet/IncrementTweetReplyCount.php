<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\TweetReplied;

class IncrementTweetReplyCount
{
    /**
     * Handle the event.
     *
     * @param TweetReplied $event
     * @return void
     */
    public function handle(TweetReplied $event)
    {
        $event->tweet->increment('replies');
    }
}
