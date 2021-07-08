<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UnfollowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_unfollow_another_user()
    {
        $user = User::factory()
            ->has(User::factory(), 'followings')
            ->create();

        Sanctum::actingAs($user);

        $this->deleteJson(route('users.follow.destroy', ['user' => $user->followings->first()->username]))
            ->assertNoContent();

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
        ]);
    }

    /** @test */
    public function guests_can_not_unfollow_anyone()
    {
        $user = User::factory()->create();

        $this->deleteJson(route('users.follow.destroy', ['user' => $user->username]))
            ->assertUnauthorized();
    }

    /** @test */
    public function users_can_not_unfollow_a_user_that_does_not_exist()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->deleteJson(route('users.follow.destroy', ['user' => Str::random()]))
            ->assertNotFound();
    }

    /** @test */
    public function users_can_not_unfollow_a_user_that_has_not_followed_before()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $this->deleteJson(route('users.follow.destroy', ['user' => $anotherUser->username]))
            ->assertStatus(422);
    }

    /** @test */
    public function when_users_unfollow_another_user_their_following_count_and_the_users_followers_count_will_decrement()
    {
        $user1 = User::factory()
            ->has(User::factory()->state(['followers_count' => 1]), 'followings')
            ->create(['followings_count' => 1]);
        $user2 = $user1->followings->first();

        Sanctum::actingAs($user1);

        $this->deleteJson(route('users.follow.destroy', ['user' => $user2->username]))
            ->dump()
            ->assertNoContent();

        $this->assertEquals(0, $user1->fresh()->followings_count);
        $this->assertEquals(0, $user1->fresh()->followers_count);
        $this->assertEquals(0, $user2->fresh()->followers_count);
        $this->assertEquals(0, $user2->fresh()->followings_count);
    }
}
