<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        $posts = Post::factory(5)->for($community)->create();

        $this->get('/r/' . $community->slug)
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->hasAll([
                    'posts.data.0.id',
                    'posts.data.0.slug',
                    'posts.data.0.title',
                    'posts.data.0.description',
                    'posts.data.0.username'
                ])
            );
    }
}
