<?php

use App\Elasticsearch\Jobs\IndexDocument;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\InteractsWithElasticsearch;

uses(InteractsWithElasticsearch::class);

test('when users tweet sth it will be indexed in elasticsearch', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $this
        ->postJson(route('tweets.store'), ['text' => $this->faker->text(60000)])
        ->assertCreated();

    $tweet = Tweet::query()->latest('id')->first()->toArray();

    $this->assertElasticsearchHas('tweets', [
        'id' => $tweet['id'],
        'text' => $tweet['text'],
        'created_at' => $tweet['created_at'],
        'user_id' => $user['id'],
        'user_ip' => '127.0.0.1'
    ]);
});

test('when users tweet sth it will be indexed in elasticsearch via queue', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    Queue::fake();

    $this
        ->postJson(route('tweets.store'), ['text' => $this->faker->text(60000)])
        ->assertCreated();

    Queue::assertPushedOn(config('elasticsearch.queue'), IndexDocument::class);
});

test('when users reply a tweet it will be indexed in elasticsearch', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    $this->postJson(
        route('tweets.replies.store', ['tweet' => $tweet->id]),
        ['text' => $text = $this->faker->text(60_000)]
    )->assertCreated();

    $reply = Tweet::query()
        ->latest('id')
        ->where('parent_tweet_id', $tweet->id)
        ->first()
        ->toArray();

    $this->assertElasticsearchHas('tweets', [
        'id' => $reply['id'],
        'text' => $reply['text'],
        'created_at' => $reply['created_at'],
        'parent_tweet_id' => $reply['parent_tweet_id'],
        'user_id' => $user['id'],
        'user_ip' => '127.0.0.1'
    ]);
});

test('when users reply a tweet it will be indexed in elasticsearch via queue', function () {
    $user = User::factory()->create();
    $tweet = Tweet::factory()->create();

    Sanctum::actingAs($user);

    Queue::fake();

    $this->postJson(
        route('tweets.replies.store', ['tweet' => $tweet->id]),
        ['text' => $text = $this->faker->text(60_000)]
    )->assertCreated();

    Queue::assertPushedOn(config('elasticsearch.queue'), IndexDocument::class);
});
