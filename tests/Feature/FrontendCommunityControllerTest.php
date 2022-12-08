<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use App\Models\PostVote;
use App\Models\Community;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class FrontendCommunityControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticateUserCanVisitfrontendCommunityPage()
    {
        $community = Community::factory()->create();

        $this->get('/r/' . $community->slug)
            ->assertOk();
    }

    /** @test */
    public function frontendCommunityShowsProperly()
    {
        $community = Community::factory()->create();

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'community.id' => $community->id,
                    'community.slug' => $community->slug,
                    'community.name' => $community->name,
                    'community.description' => $community->description,
                    'community.user_id' => $community->user->id,
                ])
        );
    }

    /** @test */
    public function frontendCommunityShowsPostsProperly()
    {
        $community = Community::factory()->create();
        Post::factory(5)->for($community)->create();

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts.data', 3)
        );
    }

    /** @test */
    public function itListsOnlyCommunityPostsInfoThatWeExpect()
    {
        $community = Community::factory()->create();
        Post::factory(5)->for($community)->create();

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->hasAll([
                    'posts.data.0.id',
                    'posts.data.0.slug',
                    'posts.data.0.title',
                    'posts.data.0.description',
                    'posts.data.0.username',
                    'posts.data.0.votes',
                    'posts.data.0.postVotes'
                ])
            );
    }

    /** @test */
    public function itShowsCommunityPostVotesCountProperly()
    {
        $community = Community::factory()->create();
        Post::factory()->for($community)->create([
            'votes' => 10
        ]);

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->where('posts.data.0.votes', 10)
            );
    }

    /** @test */
    public function itShowsCommunityPostPostVotesWhenLoadedIfUserIsAuthenticated()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->for($community)->create();
        $postVote = PostVote::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id
        ]);

        Sanctum::actingAs($user, ['*']);

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts.data.0.postVotes', 1)
                ->whereAll([
                    'posts.data.0.postVotes.0.id' => $postVote->id,
                    'posts.data.0.postVotes.0.post_id' => $post->id,
                    'posts.data.0.postVotes.0.user_id' => $user->id,
                    'posts.data.0.postVotes.0.vote' => $postVote->vote
                ])
                ->dump()
            );
    }
}
