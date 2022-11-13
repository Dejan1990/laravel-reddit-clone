<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PostVote>
 */
class PostVoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $votes = [-1, 1];

        return [
            'user_id' => User::factory(),
            'post_id' => Post::factory(),
            'vote' => $votes[rand(0, 1)]
        ];
    }

    public function seedData()
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => rand(1, 10),
            'post_id' => rand(1, 30)
        ]);
    }
}
