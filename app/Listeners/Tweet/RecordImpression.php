<?php

namespace App\Listeners\Tweet;

use App\Events\Tweet\TweetVisited;

class RecordImpression
{
    /**
     * Handle the event.
     *
     * @param TweetVisited $event
     * @return void
     */
    public function handle(TweetVisited $event)
    {
        $event
            ->tweet
            ->impressions()
            ->attach(
                $event->user,
                ['visited_at' => now(), 'ip' => $event->ip, 'agent' => $event->agent]
            );
    }
}
