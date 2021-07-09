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

        $this->postJson(route('users.images.store'), ['image' => $file, 'collection' => 'avatar'])
            ->assertNoContent();

        $this->assertDatabaseHas('media', [
            'model_type' => User::class,
            'model_id' => $user->id,
            'collection_name' => 'avatar',
            'name' => 'test-avatar',
        ]);

        Queue::assertPushed(PerformConversionsJob::class);

        $file = UploadedFile::fake()->image('test-wall.jpg');

        $this->postJson(route('users.images.store'), ['image' => $file, 'collection' => 'wall'])
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
            route('users.images.store'),
            ['image' => $file, 'collection' => $collection = $this->faker->randomElement(['avatar', 'wall'])]
        )->assertNoContent();

        $this->assertDatabaseCount('media', 1);

        $file = UploadedFile::fake()->image('test-avatar.jpg');
        $this->postJson(route('users.images.store'), ['image' => $file, 'collection' => $collection])
            ->assertNoContent();

        $this->assertDatabaseCount('media', 1);
    }

    /** @test */
    public function users_can_delete_their_avatar_or_wall()
    {
        $user = User::factory()->create();

        $user->addMedia(UploadedFile::fake()->image('test-avatar.jpg'))->toMediaCollection('avatar');
        $user->addMedia(UploadedFile::fake()->image('test-wall.jpg'))->toMediaCollection('wall');

        Sanctum::actingAs($user);

        $this->deleteJson(route('users.images.destroy'), ['collection' => 'avatar'])
            ->assertNoContent();

        $this->assertDatabaseMissing('media',[
            'model_type' => User::class,
            'model_id' => $user->id,
            'collection_name' => 'avatar',
        ]);

        $this->deleteJson(route('users.images.destroy'), ['collection' => 'wall'])
            ->assertNoContent();

        $this->assertDatabaseMissing('media',[
            'model_type' => User::class,
            'model_id' => $user->id,
            'collection_name' => 'wall',
        ]);
    }
}
