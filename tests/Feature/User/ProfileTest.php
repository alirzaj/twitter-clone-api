<?php

use App\Models\Tweet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('users can see another users list of tweets', function () {
    $viewer = User::factory()->create();
    Sanctum::actingAs($viewer);

    $user = User::factory()
        ->has(Tweet::factory()->count(4), 'tweets')
        ->has(Tweet::factory()->count(5), 'retweets')
        ->has(
            Tweet::factory()
                ->state(fn(array $attributes) => ['parent_tweet_id' => Tweet::factory()])
                ->count(6),
            'replies'
        )
        ->create();

    $userTweetsAndRetweets = $user
        ->retweets
        ->each(fn($retweet) => $retweet->created_at = $retweet->pivot->retweeted_at)
        ->merge($user->tweets)
        ->sortByDesc('created_at');

    $response = $this->getJson(route('users.tweets.index', ['user' => $user]))->assertOk();

    $userTweetsAndRetweets->each(function ($tweet, $key) use ($response) {
        $response->assertJson([
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
        ]);
    });
})->group('t');
