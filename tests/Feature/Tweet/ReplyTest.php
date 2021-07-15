<?php

namespace Tests\Feature\Tweet;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReplyTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_reply_a_tweet()
    {
        $user = User::factory()->create();
        $tweet = Tweet::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(
            route('tweets.replies.store', ['tweet' => $tweet->id]),
            ['text' => $text = $this->faker->text(60_000)]
        )->assertCreated();

        $this->assertDatabaseHas('tweets', [
            'parent_tweet_id' => $tweet->id,
            'user_id' => $user->id,
            'text' => $text
        ]);

        $this->assertDatabaseHas('tweets', [
            'id' => $tweet->id,
            'replies_count' => $tweet->replies_count + 1
        ]);
    }

    /** @test */
    public function users_cant_reply_a_tweet_that_does_not_exist()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(
            route('tweets.replies.store', ['tweet' => $this->faker->randomNumber()]),
            ['text' => $this->faker->text(60_000)]
        )->assertNotFound();
    }

    /** @test */
    public function guests_can_not_reply_a_tweet()
    {
        $tweet = Tweet::factory()->create();

        $this->postJson(
            route('tweets.replies.store', ['tweet' => $tweet->id]),
            ['text' => $this->faker->text(60_000)]
        )->assertUnauthorized();
    }
}
