<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Community>
 */
class CommunityFactory extends Factory
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
            'name' => fake()->unique()->words(rand(1, 3), true),
            'description' => fake()->words(rand(10, 17), true)
        ];
    }

    public function seedData()
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => rand(1, 10)
        ]);
    }
}
