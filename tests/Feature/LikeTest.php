<?php

use App\Models\Tweet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('users can like a tweet', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('tweets.likes.store', ['tweet' => $tweet]))
        ->assertCreated();

    $this->assertDatabaseHas('likes', [
        'user_id' => $user->id,
        'tweet_id' => $tweet->id,
    ]);

    expect($tweet->fresh()->likes_count)->toBe($tweet->likes_count + 1);
});

test('users can not like a tweet twice', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('tweets.likes.store', ['tweet' => $tweet]))
        ->assertCreated();

    $this->postJson(route('tweets.likes.store', ['tweet' => $tweet]))
        ->assertUnauthorized();
});

test('users can take their like back', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('tweets.likes.store', ['tweet' => $tweet]))
        ->assertCreated();

    $this->deleteJson(route('tweets.likes.destroy', ['tweet' => $tweet]))
        ->assertCreated();

    $this->assertDatabaseMissing('likes', [
        'user_id' => $user->id,
        'tweet_id' => $tweet->id
    ]);

    expect($tweet->fresh()->likes_count)->toBe($tweet->likes_count);
});

test('users can not take their like back from a tweet unless they have liked it before', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    $this->deleteJson(route('tweets.likes.destroy', ['tweet' => $tweet]))
        ->assertUnauthorized();
});

test('guests can not like a tweet', function () {
    $tweet = Tweet::factory()->create();

    $this->postJson(route('tweets.likes.store', ['tweet' => $tweet]))
        ->assertUnauthorized();

    $this->deleteJson(route('tweets.likes.destroy', ['tweet' => $tweet]))
        ->assertUnauthorized();
});

test('users can not like a tweet that does not exist', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(route('tweets.likes.store', ['tweet' => abs(rand()) + 1]))
        ->assertNotFound();

    $this->deleteJson(route('tweets.likes.destroy', ['tweet' => abs(rand()) + 1]))
        ->assertNotFound();
});
