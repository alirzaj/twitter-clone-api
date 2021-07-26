<?php

namespace Tests\Feature\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test
     * @throws \Throwable
     */
    public function when_users_see_their_home_they_see_latest_tweets_and_replies_and_retweets_of_their_followings()
    {
        $user = User::factory()
            ->has(
                User::factory()
                    ->has(Tweet::factory()->count(5), 'tweets')
                    ->has(Tweet::factory()->count(3), 'retweets')
                    ->has(
                        Tweet::factory()
                            ->state(fn(array $attributes) => ['parent_tweet_id' => Tweet::factory()])
                            ->count(2),
                        'replies'
                    )
                    ->count(3),
                'followings'
            )
            ->create();

        Sanctum::actingAs($user);

        $response = $this->getJson(route('home.index'));
        //  $response->dump();
        $response->assertOk();

        $followedUsersTweets = $user
            ->followings
            ->map(
                fn($following) => $following->tweets()->latest()->get()->toArray()
            )
            ->flatten(1);

        $followedUsersReplies = $user
            ->followings
            ->map(
                fn($following) => $following->replies()->latest()->get()->toArray()
            )
            ->flatten(1);

        $followedUsersRetweets = $user
            ->followings
            ->map(
                fn($following) => $following->retweets()->latest()->get()->toArray()
            )
            ->flatten(1);

        $tweetsInFeed = collect()
            ->merge($followedUsersTweets)
            ->merge($followedUsersReplies)
            ->merge($followedUsersRetweets)
            ->sortByDesc('created_at')
            ->values();

        $tweetsInFeed
            ->take(15)
            ->each(
                fn($tweet, $key) => $response->assertJson([
                    'data' => [
                        $key => [
                            'id' => $tweet['id'],
                            'text' => $tweet['text'],
                            'user' => with(User::query()->find($tweet['user_id']), fn($user) => [
                                'name' => $user->name,
                                'username' => $user->username,
                                'avatar' => $user->avatar_thumbnail,
                            ]),
                            'parent_tweet_id' => $tweet['parent_tweet_id'],
                            'retweets_count' => $tweet['retweets_count'],
                            'replies_count' => $tweet['replies_count'],
                            'created_at' => $tweet['created_at'],
                            'updated_at' => $tweet['updated_at'],
                        ]
                    ]
                ])
            );
    }
}
