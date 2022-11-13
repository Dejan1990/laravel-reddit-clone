<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'community_id' => Community::factory(),
            'title' => fake()->words(rand(3, 6), true),
            'description' => fake()->paragraphs(rand(2, 5), true),
            'url' => fake()->url()
        ];
    }

    public function seedData()
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => rand(1, 10),
            'community_id' => rand(1, 15)
        ]);
    }
}
