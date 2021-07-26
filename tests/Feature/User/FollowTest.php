<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FollowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_follow_another_user()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Sanctum::actingAs($user1);

        $this->postJson(route('users.follow.store', ['user' => $user2->username]))
            ->assertNoContent();

        $this->assertEquals($user1->followings->first()->id, $user2->id);

        $this->assertDatabaseHas('follows', [
            'follower_id' => $user1->id,
            'following_id' => $user2->id,
            'followed_at' => now()->toDateTimeString()
        ]);
    }

    /** @test */
    public function guests_can_not_follow_anyone()
    {
        $user = User::factory()->create();

        $this->postJson(route('users.follow.store', ['user' => $user->username]))
            ->assertUnauthorized();
    }

    /** @test */
    public function users_can_not_follow_a_user_that_does_not_exist()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('users.follow.store', ['user' => Str::random()]))
            ->assertNotFound();
    }

    /** @test */
    public function users_can_not_follow_another_user_twice()
    {
        $user = User::factory()
            ->has(User::factory(), 'followings')
            ->create();

        Sanctum::actingAs($user);

        $this->postJson(route('users.follow.store', ['user' => $user->followings->first()->username]))
            ->assertStatus(422);
    }

    /** @test */
    public function when_users_follow_another_user_their_following_count_and_the_users_followers_count_will_increment()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Sanctum::actingAs($user1);

        $this->postJson(route('users.follow.store', ['user' => $user2->username]))
            ->assertNoContent();

        $this->assertEquals(1, $user1->fresh()->followings_count);
        $this->assertEquals(0, $user1->fresh()->followers_count);
        $this->assertEquals(1, $user2->fresh()->followers_count);
        $this->assertEquals(0, $user2->fresh()->followings_count);
    }

    /** @test */
    public function users_can_see_a_list_of_a_users_followers()
    {
        //create a user with 16 followers
        $user1 = User::factory()
            ->has(User::factory()->state(['followings_count' => 1])->count(16), 'followers')
            ->create(['followers_count' => 16]);

        //create a user with 1 random follower (to assert its not in response) &
        // 2 following of user1's followers (to assert following attribute is correct)
        //& 2 followers of user1's followers to assert follows attribute
        $user2 = User::factory()
            ->has(User::factory()->state(['followings_count' => 1]), 'followers')
            ->create(['followers_count' => 3, 'followings_count' => 2]);

        $user2->followings()->attach([$user1->followers[0]->id, $user1->followers[3]->id]);
        $user2->followers()->attach([$user1->followers[4]->id, $user1->followers[15]->id]);

        //when user2 sees user1 followers
        Sanctum::actingAs($user2);

        //order is correct & followed attribute works fine
        $response = $this->getJson(route('users.followers.index', ['user' => $user1->username]))
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'name' => $user1->followers[0]->name,
                        'username' => $user1->followers[0]->username,
                        'bio' => $user1->followers[0]->bio,
                        'avatar' => $user1->followers[0]->avatar,
                        'following' => true,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followers[1]->name,
                        'username' => $user1->followers[1]->username,
                        'bio' => $user1->followers[1]->bio,
                        'avatar' => $user1->followers[1]->avatar,
                        'following' => false,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followers[2]->name,
                        'username' => $user1->followers[2]->username,
                        'bio' => $user1->followers[2]->bio,
                        'avatar' => $user1->followers[2]->avatar,
                        'following' => false,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followers[3]->name,
                        'username' => $user1->followers[3]->username,
                        'bio' => $user1->followers[3]->bio,
                        'avatar' => $user1->followers[3]->avatar,
                        'following' => true,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followers[4]->name,
                        'username' => $user1->followers[4]->username,
                        'bio' => $user1->followers[4]->bio,
                        'avatar' => $user1->followers[4]->avatar,
                        'following' => false,
                        'follows' => true,
                    ],
                ]
            ])
            ->decodeResponseJson();

        $this->getJson($response['links']['next'])
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'name' => $user1->followers[15]->name,
                        'username' => $user1->followers[15]->username,
                        'bio' => $user1->followers[15]->bio,
                        'avatar' => $user1->followers[15]->avatar,
                        'following' => false,
                        'follows' => true,
                    ]
                ]
            ]);
    }

    /** @test */
    public function users_can_not_follow_themselves()
    {
        $user1 = User::factory()->create();

        Sanctum::actingAs($user1);

        $this->postJson(route('users.follow.store', ['user' => $user1->username]))
            ->assertStatus(422);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user1->id,
            'following_id' => $user1->id,
        ]);
    }

    /** @test */
    public function users_can_see_a_list_of_another_users_followings()
    {
        $user1 = User::factory()
            ->has(User::factory()->state(['followers_count' => 1])->count(16), 'followings')
            ->create(['followings_count' => 16]);

        $user2 = User::factory()->create(['followers_count' => 2, 'followings_count' => 2]);

        $user2->followings()->attach([$user1->followings[0]->id, $user1->followings[3]->id]);
        $user2->followers()->attach([$user1->followings[1]->id, $user1->followings[2]->id]);

        Sanctum::actingAs($user2);

        $response = $this->getJson(route('users.followings.index', ['user' => $user1->username]))
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'name' => $user1->followings[0]->name,
                        'username' => $user1->followings[0]->username,
                        'bio' => $user1->followings[0]->bio,
                        'avatar' => $user1->followings[0]->avatar,
                        'following' => true,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followings[1]->name,
                        'username' => $user1->followings[1]->username,
                        'bio' => $user1->followings[1]->bio,
                        'avatar' => $user1->followings[1]->avatar,
                        'following' => false,
                        'follows' => true,
                    ],
                    [
                        'name' => $user1->followings[2]->name,
                        'username' => $user1->followings[2]->username,
                        'bio' => $user1->followings[2]->bio,
                        'avatar' => $user1->followings[2]->avatar,
                        'following' => false,
                        'follows' => true,
                    ],
                    [
                        'name' => $user1->followings[3]->name,
                        'username' => $user1->followings[3]->username,
                        'bio' => $user1->followings[3]->bio,
                        'avatar' => $user1->followings[3]->avatar,
                        'following' => true,
                        'follows' => false,
                    ],
                    [
                        'name' => $user1->followings[4]->name,
                        'username' => $user1->followings[4]->username,
                        'bio' => $user1->followings[4]->bio,
                        'avatar' => $user1->followings[4]->avatar,
                        'following' => false,
                        'follows' => false,
                    ],
                ]
            ])->decodeResponseJson();

        $this->getJson($response['links']['next'])
            ->assertOk()
            ->assertJson([
                'data' => [
                    [
                        'name' => $user1->followings[15]->name,
                        'username' => $user1->followings[15]->username,
                        'bio' => $user1->followings[15]->bio,
                        'avatar' => $user1->followings[15]->avatar,
                        'following' => false,
                        'follows' => false,
                    ]
                ]
            ]);
    }
}
