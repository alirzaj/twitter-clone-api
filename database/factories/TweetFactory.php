<?php

namespace Database\Factories;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TweetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tweet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'text' => $this->faker->text(500),
            'likes_count' => $this->faker->randomNumber(),
            'impressions_count' => $this->faker->randomNumber(),
            'retweets_count' => $this->faker->randomNumber(),
            'replies_count' => $this->faker->randomNumber(),
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}
