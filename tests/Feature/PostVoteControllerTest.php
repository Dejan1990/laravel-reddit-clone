<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class PostVoteControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticateUserCannotUpVote()
    {
        $post = Post::factory()->create();

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect('/login');

        $this->assertDatabaseEmpty('post_votes');
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 0
        ]);
    }

    /** @test */
    public function unauthenticateUserCannotDownVote()
    {
        $post = Post::factory()->create();

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect('/login');

        $this->assertDatabaseEmpty('post_votes');
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 0
        ]);
    }

    /** @test */
    public function authenticateUserCanUpVote()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 1
        ]);
    }

    /** @test */
    public function authenticateUserCanDownVote()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => -1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => -1
        ]);
    }

    /** @test */
    public function userCannotUpVoteTwiceSamePost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 1
        ]);

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 1
        ]);
    }

    /** @test */
    public function userCannotDownVoteTwiceSamePost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => -1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => -1
        ]);

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => -1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => -1
        ]);
    }

    /** @test */
    public function userCanChangeUpVoteToDownVoteForSamePost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 1
        ]);

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => -1
        ]);
    
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => -1
        ]);
    }

    /** @test */
    public function userCanChangeDownVoteToUpVoteForSamePost()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('posts.downVote', $post->slug), ['vote' => -1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => -1
        ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => -1
        ]);

        $this->post(route('posts.upVote', $post->slug), ['vote' => 1])
            ->assertRedirect();

        $this->assertDatabaseHas('post_votes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'vote' => 1
        ]);
    
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'votes' => 1
        ]);
    }
}
