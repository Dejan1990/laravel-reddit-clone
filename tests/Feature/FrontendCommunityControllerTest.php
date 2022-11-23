<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FrontendCommunityControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function frontendCommunityShowProperly()
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
}
