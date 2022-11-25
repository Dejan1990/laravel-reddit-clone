<?php

namespace Tests\Feature;

use App\Models\Community;
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

        $this->get(route('communities.posts.create', $community->id))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCanVisitCreatePostPage()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->get('/communities/'.$community->id.'/posts/create')
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
}
