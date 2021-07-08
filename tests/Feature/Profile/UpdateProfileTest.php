<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_update_their_profile()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->patchJson(route('profile.update'), $attributes = User::factory()->raw())->assertNoContent();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $attributes['name'],
            'username' => $attributes['username'],
            'email' => $attributes['email'],
            'bio' => $attributes['bio'],
            'location' => $attributes['location'],
            'birthday' => $attributes['birthday'],
            'phone' => $attributes['phone'],
        ]);
    }

    /** @test */
    public function guests_can_not_update_their_profile()
    {
        $this->patchJson(route('profile.update'),  User::factory()->raw())->assertUnauthorized();
    }
}
