<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontendPostControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticateUserCanVisitfrontendPostShowPage()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        $this->get(route('frontend.communities.posts.show', [$community->slug, $post->slug]))
            ->assertOk();
    }

    /** @test */
    public function frontendPostShowsProperly()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        $this->get(route('frontend.communities.posts.show', [$community->slug, $post->slug]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->hasAll('post', 'community')
        );
    }

    /** @test */
    public function itListsOnlyPostsInfoThatWeExpect()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();

        $this->get(route('frontend.communities.posts.show', [$community->slug, $post->slug]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'post.data.id' => $post->id,
                    'post.data.slug' => $post->slug,
                    'post.data.title' => $post->title,
                    'post.data.url' => $post->url,
                    'post.data.description' => $post->description,
                    'post.data.username' => $post->user->username
                ])
                ->missing('community_id')
        );
    }
}
