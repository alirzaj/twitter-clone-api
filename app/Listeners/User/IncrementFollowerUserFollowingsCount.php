<?php

namespace App\Listeners\User;

use App\Events\User\UserFollowed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IncrementFollowerUserFollowingsCount
{
    /**
     * Handle the event.
     *
     * @param  UserFollowed  $event
     * @return void
     */
    public function handle(UserFollowed $event)
    {
        $event->follower->increment('followings_count');
    }
}
