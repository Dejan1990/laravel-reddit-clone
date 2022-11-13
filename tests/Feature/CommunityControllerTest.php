<?php

namespace Tests\Feature;

use App\Models\Community;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

class CommunityControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    /** @test */
    public function unauthenticateUserCannotVisitIndexPage()
    {
        $response = $this->get('/communities')
            ->assertRedirect('/login');
    }

    /** @test */
    public function onlyAuthenticateUserCanVisitIndexPage()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/communities')
            ->assertOk();
    }

    /** @test */
    public function unauthenticateUserCannotVisitCreatePage()
    {
        $response = $this->get('/communities/create')
            ->assertRedirect('/login');
    }

    /** @test */
    public function onlyAuthenticateUserCanVisitCreatePage()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->get('/communities/create')
            ->assertOk();
    }

    /** @test */
    public function validationWorksForCommunityStore()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->post('/communities', []);

        $response->assertSessionHasErrors(['name', 'description']);
    }

    /** @test */
    public function authenticateUserCanCreateCommunity()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $response = $this->post('/communities', Community::factory()->raw());

        $response->assertRedirect('/communities');
        $this->assertDatabaseCount('communities', 1);
        $this->assertDatabaseHas('communities', [
            'user_id' => $user->id
        ]);
    }
}
