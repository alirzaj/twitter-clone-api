<?php

namespace Tests\Feature\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RetweetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_retweet_a_tweet()
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('tweets.retweets.store', ['tweet' => $tweet->id]))
            ->assertCreated();

        $this->assertDatabaseHas('retweets', [
            'user_id' => $user->id,
            'tweet_id' => $tweet->id,
            'retweeted_at' => now()->toDateTimeString()
        ]);

        $this->assertTrue($user->retweets()->where('id', $tweet->id)->exists());
    }

    /** @test */
    public function when_users_retweet_a_tweet_its_retweet_count_will_increment()
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('tweets.retweets.store', ['tweet' => $tweet->id]))
            ->assertCreated();

        $this->assertEquals($tweet->retweets_count + 1, $tweet->fresh()->retweets_count);
    }

    /** @test */
    public function guests_can_not_retweet_a_tweet()
    {
        $tweet = Tweet::factory()->create();

        $this->postJson(route('tweets.retweets.store', ['tweet' => $tweet->id]))
            ->assertUnauthorized();
    }

    /** @test */
    public function users_can_not_retweet_a_tweet_more_than_once()
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('tweets.retweets.store', ['tweet' => $tweet->id]))
            ->assertCreated();

        $this->postJson(route('tweets.retweets.store', ['tweet' => $tweet->id]))
            ->assertStatus(422);

        $this->assertCount(1, $tweet->retweets()->where('user_id', $user->id)->get());
        $this->assertEquals($tweet->retweets_count + 1, $tweet->fresh()->retweets_count);
    }
}
