<?php

namespace Tests\Feature\Authentication;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_register()
    {
        Event::fake();

        $user = [
            'name' => $this->faker->name(),
            'username' => $this->faker->userName(),
            'email' => $this->faker->email(),
            'password' => $password = Str::random() . Str::upper($this->faker->randomLetter()) . $this->faker->randomNumber(),
            'password_confirmation' => $password,
        ];

        $this->postJson(route('register'), $user)
            ->assertCreated()
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseHas('users', [
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'email_verified_at' => null
        ]);

        $this->assertTrue(
            Hash::check(
                $user['password'],
                User::query()->firstWhere('username', $user['username'])->password
            )
        );

        Event::assertDispatched(Registered::class);
    }
    //todo validation test
}
