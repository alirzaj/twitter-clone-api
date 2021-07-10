<?php

namespace Tests\Feature\Tweet;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TweetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_create_tweets()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(
            route('tweets.store'),
            ['text' => $text = $this->faker->text(60000)]
        )->assertCreated();

        $this->assertDatabaseHas('tweets', [
            'user_id' => $user->id,
            'text' => $text
        ]);
    }
}
