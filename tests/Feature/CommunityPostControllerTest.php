<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommunityPostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticateUserCannotVisitPostCreatePage()
    {
        $community = Community::factory()->create();

        $this->get(route('communities.posts.create', $community->slug))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCanVisitCreatePostPage()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->get(route('communities.posts.create', $community->slug))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'community.id' => $community->id,
                    'community.name' => $community->name,
                    'community.slug' => $community->slug,
                    'community.description' => $community->description,
                    'community.user_id' => $community->user->id
                ])
        );
    }

    /** @test */
    public function unauthenticateUserCannotCreatePost()
    {
        $community = Community::factory()->create();

        $this->post(route('communities.posts.store', $community->slug), Post::factory()->raw())
            ->assertRedirect('/login');

        $this->assertDatabaseCount('posts', 0);
    }

    /** @test */
    public function validationWorksForPostCreate()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('communities.posts.store', $community->slug), [])
            ->assertSessionHasErrors(['title', 'description']);

        $this->assertDatabaseCount('posts', 0);
    }

    /** @test */
    public function authenticateUserCanCreatePost()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->post(route('communities.posts.store', $community->slug), Post::factory()->raw())
            ->assertRedirect(route('frontend.community.show', $community->slug))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'community_id' => $community->id
        ]);
    }

    /** @test */
    public function unauthenticateUserCannotVisitEditPostPage()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->create();

        $this->get(route('communities.posts.edit', [$community, $post]))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCanVisitEditPostPage()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->get(route('communities.posts.edit', [$community, $post]))
            ->assertOk();
    }
}
