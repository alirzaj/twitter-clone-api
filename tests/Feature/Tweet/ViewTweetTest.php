<?php

namespace Tests\Feature\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ViewTweetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_see_a_tweet()
    {
        Sanctum::actingAs(User::factory()->create());

        $tweet = Tweet::factory()->create();

        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => $tweet->id,
                    'text' => $tweet->text,
                    'likes' => $tweet->likes,
                    'impressions_count' => $tweet->impressions_count + 1,
                    'retweets_count' => $tweet->retweets_count,
                    'replies_count' => $tweet->replies_count,
                    'user' => [
                        'name' => $tweet->user->name,
                        'username' => $tweet->user->username,
                    ]
                ]
            ])
            ->assertJsonStructure(['data' => ['user' => ['avatar'], 'created_at', 'updated_at']]);
    }

    /** @test */
    public function when_a_user_visits_a_tweet_an_impression_is_recorded()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tweet = Tweet::factory()->create();

        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertOk();

        $this->assertDatabaseHas('impressions', [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id,
            'ip' => '127.0.0.1',
            'agent' => 'Symfony',
            'visited_at' => now()->toDateTimeString(),
        ]);

        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
            'impressions_count' => $tweet->impressions_count + 1,
        ]);
    }

    /** @test */
    public function when_a_user_visits_a_tweet_more_than_once_impression_is_not_re_recorded()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $tweet = Tweet::factory()->create();

        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertOk();
        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertOk();
        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertOk();

        $this->assertEquals(1, $tweet->impressions()->where('user_id', $user->id)->count());

        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
            'impressions_count' => $tweet->impressions_count + 1,
        ]);
    }

    /** @test */
    public function guests_can_not_see_a_tweet()
    {
        $tweet = Tweet::factory()->create();

        $this->getJson(route('tweets.show', ['tweet' => $tweet->id]))
            ->assertUnauthorized();
    }
}
