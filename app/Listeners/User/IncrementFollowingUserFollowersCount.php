<?php

namespace App\Listeners\User;

use App\Events\User\UserFollowed;

class IncrementFollowingUserFollowersCount
{
    /**
     * Handle the event.
     *
     * @param UserFollowed $event
     * @return void
     */
    public function handle(UserFollowed $event)
    {
        $event->following->increment('followers_count');
    }
}
