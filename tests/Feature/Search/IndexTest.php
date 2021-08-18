<?php

use App\Models\Tweet;
use App\Models\User;
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
