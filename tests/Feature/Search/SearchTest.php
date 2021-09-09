<?php

use App\Models\Tweet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('when users search for sth they will get relevant results', function () {
    $tweet1 = Tweet::factory()->create(['text' => 'i live in italy']);
    $tweet2 = Tweet::factory()->create(['text' => 'iran is a good country']);
    $tweet3 = Tweet::factory()->create(['text' => 'italya is a fun country to live in']);
    $tweet4 = Tweet::factory()->create(['text' => 'pasta is a delicious meal']);

    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->getJson(route('search.show', ['q' => 'italy', 'type' => 'tweet']))
        ->assertOk()
        ->assertJson([
            'users' => [],
            'tweets' => [
                [
                    'id' => $tweet1->id,
                    'text' => $tweet1->text,
                    'user' => [
                        'name' => $tweet1->user->name,
                        'username' => $tweet1->user->username,
                        'avatar' => null,
                    ]
                ],
                [
                    'id' => $tweet3->id,
                    'text' => $tweet3->text,
                    'user' => [
                        'name' => $tweet3->user->name,
                        'username' => $tweet3->user->username,
                        'avatar' => null,
                    ]
                ]
            ],
        ]);
});
