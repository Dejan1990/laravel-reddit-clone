<?php

namespace Tests\Feature;

use App\Models\Comment;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Community;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

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
        $this->withoutExceptionHandling();
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
                    'post.data.username' => $post->user->username,
                    'post.data.owner' => false
                ])
                ->missing('community_id')
        );
    }

    /** @test */
    public function itListsCommentsWithExpectedInfoIfTheyExist()
    {
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->hasComments(2)->create();

        $this->get(route('frontend.communities.posts.show', [$community->slug, $post->slug]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->hasAll([
                    'post.data.comments' => 2,
                    'post.data.comments.0.username',
                    'post.data.comments.0.content',
                    'post.data.comments.1.username',
                    'post.data.comments.1.content'
                ])
        );
    }
}
