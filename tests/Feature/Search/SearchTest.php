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

    $this->getJson(route('search.show', ['q' => 'ital', 'type' => 'tweet']))
        ->assertOk()
        ->assertJson([
            'data' => [
                [
                    'id' => $tweet1->id
                ],
                [
                    'id' => $tweet2->id
                ]
            ]
        ]);
});
