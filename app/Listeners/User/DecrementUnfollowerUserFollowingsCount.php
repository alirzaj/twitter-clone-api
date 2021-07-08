<?php

namespace App\Listeners\User;

use App\Events\User\UserUnfollowed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DecrementUnfollowerUserFollowingsCount
{
    /**
     * Handle the event.
     *
     * @param  UserUnfollowed  $event
     * @return void
     */
    public function handle(UserUnfollowed $event)
    {
        $event->unfollower->decrement('followings_count');
    }
}
