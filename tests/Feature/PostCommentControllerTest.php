<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostCommentControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticateUserCannotCreateComment()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        $this->post(route('frontend.posts.comments', [$community->slug, $post->slug]))
            ->assertRedirect('/login');

        $this->assertDatabaseEmpty('comments');
    }

    /** @test */
    public function validationWorksForCommentCreate()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('frontend.posts.comments', [$community->slug, $post->slug]), [])
            ->assertSessionHasErrors(['content']);

        $this->assertDatabaseEmpty('comments');
    }

    /** @test */
    public function authenticateUserCanCreateComment()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('frontend.posts.comments', [$community->slug, $post->slug]), [
            'content' => 'New comment'
        ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'content' => 'New comment'
        ]);
    }
}
