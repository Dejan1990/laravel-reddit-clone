<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Comment;
use App\Models\Community;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory(10)->create();
        Community::factory(15)->seedData()->create();
        Post::factory(30)->seedData()->create();
        
        foreach (Post::all() as $post) {
            Comment::factory(rand(2, 7))->seedData()->create([
                'post_id' => $post->id
            ]);
        }

        // Generate unique votes. Ensure idea_id and user_id are unique for each row
        foreach (range(1, 10) as $user_id) {
            foreach (range(1, 30) as $post_id) {
                if ($post_id % 2 === 0) { // ovo radimo da ne bi svaki user glasao za svaku ideju
                    PostVote::factory()->create([
                        'user_id' => $user_id,
                        'post_id' => $post_id,
                    ]);
                }
            }
        }
    }
}
