<?php

namespace Tests\Feature\Profile;

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
}
