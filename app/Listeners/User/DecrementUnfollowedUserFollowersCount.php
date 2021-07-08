<?php

namespace App\Listeners\User;

use App\Events\User\UserUnfollowed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DecrementUnfollowedUserFollowersCount
{
    /**
     * Handle the event.
     *
     * @param  UserUnfollowed  $event
     * @return void
     */
    public function handle(UserUnfollowed $event)
    {
        $event->unfollowed->decrement('followers_count');
    }
}
