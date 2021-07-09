<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Tests\TestCase;

class UserImageTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function users_can_upload_avatar_or_wall_for_themselves()
    {
        Queue::fake();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('test-avatar.jpg');

        $this->postJson(route('users.image.store'), ['image' => $file, 'collection' => 'avatar'])
            ->assertNoContent();

        $this->assertDatabaseHas('media', [
            'model_type' => User::class,
            'model_id' => $user->id,
            'collection_name' => 'avatar',
            'name' => 'test-avatar',
        ]);

        Queue::assertPushed(PerformConversionsJob::class);

        $file = UploadedFile::fake()->image('test-wall.jpg');

        $this->postJson(route('users.image.store'), ['image' => $file, 'collection' => 'wall'])
            ->assertNoContent();


        $this->assertDatabaseHas('media', [
            'model_type' => User::class,
            'model_id' => $user->id,
            'collection_name' => 'wall',
            'name' => 'test-wall',
        ]);
    }

    /** @test */
    public function when_users_upload_another_avatar_or_wall_the_old_one_will_be_deleted()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $this->postJson(
            route('users.image.store'),
            ['image' => $file, 'collection' => $collection = $this->faker->randomElement(['avatar', 'wall'])]
        )->assertNoContent();

        $this->assertDatabaseCount('media', 1);

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $this->postJson(route('users.image.store'), ['image' => $file, 'collection' => $collection])
            ->assertNoContent();

        $this->assertDatabaseCount('media', 1);
    }
}
