<?php

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Laravel\Sanctum\Sanctum;

test('users can search for tweets having a hashtag', function () {
    [$t1, $t2, $t3, $t4] = Tweet::factory()
        ->count(4)
        ->state(new Sequence(
            ['text' => 'akoowmpqo kwpk #ABCD'],
            ['text' => '#173 ijwcioj wiajdn wjidj'],
            ['text' => 'ijwcioj pkwfje #test1 wiajdn wjidj'],
            ['text' => 'aw[qdjioj iwqjdj #AB_cd woj'],
        ))
        ->create();

    Tweet::factory()->count(4)->create();

    Sanctum::actingAs(User::factory()->create());

    $this->getJson(route('search.tags.show', ['hashtag' => '#ABCD']))
        ->assertOk()
        ->assertJson([
            'data' => [
                [
                    'id' => $t1->id,
                    'text' => $t1->text,
                    'user' => [
                        'name' => $t1->user->name,
                        'username' => $t1->user->username,
                        'avatar' => null,
                    ]
                ],
            ]
        ]);
});
