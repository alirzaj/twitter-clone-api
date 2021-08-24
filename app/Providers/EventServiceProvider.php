<?php

namespace App\Providers;

use App\Events\Tweet\Retweeted;
use App\Events\Tweet\TweetLiked;
use App\Events\Tweet\TweetLikeRevoked;
use App\Events\Tweet\TweetReplied;
use App\Events\Tweet\TweetVisited;
use App\Events\User\UserFollowed;
use App\Events\User\UserUnfollowed;
use App\Listeners\Tweet\DecrementLikesCount;
use App\Listeners\Tweet\IncrementLikesCount;
use App\Listeners\Tweet\IncrementRetweetsCount;
use App\Listeners\Tweet\IncrementTweetImpressionsCount;
use App\Listeners\Tweet\IncrementTweetReplyCount;
use App\Listeners\Tweet\RecordImpression;
use App\Listeners\User\IncrementFollowingUserFollowersCount;
use App\Listeners\User\IncrementFollowerUserFollowingsCount;
use App\Listeners\User\DecrementUnfollowedUserFollowersCount;
use App\Listeners\User\DecrementUnfollowerUserFollowingsCount;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        UserFollowed::class => [
            IncrementFollowerUserFollowingsCount::class,
            IncrementFollowingUserFollowersCount::class,
        ],
        UserUnfollowed::class => [
            DecrementUnfollowerUserFollowingsCount::class,
            DecrementUnfollowedUserFollowersCount::class,
        ],
        TweetReplied::class => [
            IncrementTweetReplyCount::class,
        ],
        TweetVisited::class => [
            RecordImpression::class,
            IncrementTweetImpressionsCount::class,
        ],
        Retweeted::class => [
            IncrementRetweetsCount::class,
        ],

        TweetLiked::class => [
            IncrementLikesCount::class,
        ],

        TweetLikeRevoked::class => [
            DecrementLikesCount::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
