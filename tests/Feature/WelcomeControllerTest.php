<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostVote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function welcomePageCanBeVisited()
    {
        $this->get('/')->assertOk();
    }

    /** @test */
    public function welcomePageHasCommunitiesAsMuchAsWeExpect()
    {
        Community::factory(9)->create();

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('communities.data', 6)
        );
    }

    /** @test */
    public function welcomePageHasCommunitiesOrderByPostsCount()
    {
        $communityOne = Community::factory()->hasPosts()->create();
        $communityTwo = Community::factory()->hasPosts(3)->create();
        $communityThree = Community::factory()->hasPosts(2)->create();

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('communities.data', 3)
                ->whereAll([
                    'communities.data.0.id' => $communityTwo->id,
                    'communities.data.1.id' => $communityThree->id,
                    'communities.data.2.id' => $communityOne->id
                ])
        );
    }

    /** @test */
    public function welcomePageShowsProperlyCommunitiesInfo()
    {
        $community = Community::factory()->hasPosts(3)->create();

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'communities.data.0.id' => $community->id,
                    'communities.data.0.name' => $community->name,
                    'communities.data.0.description' => $community->description,
                    'communities.data.0.slug' => $community->slug,
                    'communities.data.0.username' => $community->user->username,
                    'communities.data.0.posts_count' => 3
                ])
        );
    }

    /** @test */
    public function welcomePageHasPostsAsMuchAsWeExpect()
    {
        Post::factory(20)->create();

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts.data', 12)
        );
    }

    /** @test */
    public function welcomePageHasPostsOrderByVotes()
    {
        $postOne = Post::factory()->create(['votes' => 2]);
        $postTwo = Post::factory()->create(['votes' => 9]);
        $postThree = Post::factory()->create(['votes' => 4]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'posts.data.0.id' => $postTwo->id,
                    'posts.data.1.id' => $postThree->id,
                    'posts.data.2.id' => $postOne->id
                ])
        );
    }

    /** @test */
    public function welcomePageShowsProperlyPostsInfo()
    {
        $user = User::factory()->create();
        $post = Post::factory()->hasComments(3)->create(['votes' => 7]);

        Sanctum::actingAs($user, ['*']);
        
        $this->get('/')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->has('posts.data')
                ->whereAll([
                    'posts.data.0.id' => $post->id,
                    'posts.data.0.title' => $post->title,
                    'posts.data.0.description' => $post->description,
                    'posts.data.0.slug' => $post->slug,
                    'posts.data.0.username' => $post->user->username,
                    'posts.data.0.comments_count' => 3,
                    'posts.data.0.community_slug' => $post->community->slug,
                    'posts.data.0.votes' => $post->votes,
                ])
        );
    }
}
