<?php

namespace Tests\Feature;

use App\Models\Comment;
use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\Community;
use App\Models\PostVote;
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
        $post = Post::factory()->for($community)->create(['votes' => 5]);

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
                    'post.data.owner' => false,
                    'post.data.votes' => $post->votes,
                    'post.data.postVotes' => $post->postVotes
                ])
                ->missing('community_id')
                ->dump()
        );
    }

    /** @test */
    public function itShowsPostPostVotesWhenLoadedIfUserIsAuthenticated()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();
        $postVotes = PostVote::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->get(route('frontend.communities.posts.show', [$community->slug, $post->slug]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('post.data.postVotes', 1)
                ->whereAll([
                    'post.data.postVotes.0.id' => $postVotes->id,
                    'post.data.postVotes.0.post_id' => $post->id,
                    'post.data.postVotes.0.user_id' => $user->id,
                    'post.data.postVotes.0.vote' => $postVotes->vote
                ])
                ->dump()
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
