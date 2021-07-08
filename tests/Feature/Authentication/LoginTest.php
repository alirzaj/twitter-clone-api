<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_login()
    {
        $user = User::factory()->create(['password' => Hash::make($password = $this->faker->password())]);

        $this->postJson(
            route('login'),
            ['email' => $user->email, 'password' => $password]
        )->assertOk()->assertJsonStructure(['data' => ['token']]);

        $this->postJson(
            route('login'),
            ['email' => $user->email, 'password' => Str::random()]
        )->assertUnauthorized();

        $this->postJson(
            route('login'),
            ['email' => $this->faker->email(), 'password' => $password]
        )->assertUnauthorized();
    }
}
