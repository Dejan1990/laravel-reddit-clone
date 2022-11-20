<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Community;
use Inertia\Testing\Assert;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

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
    public function itListsCommunitiesOnIndexPage()
    {
        $user = User::factory()->create();
        Community::factory(10)->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->get('/communities')
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->url('/communities')
                    ->has('communities.data', 3)
                    ->dump()
            );
    }

    /** @test */
    public function itListsOnlyCommunitiesInfoThatWeExpectedOnIndexPage()
    {
        $user = User::factory()->create();
        Community::factory(10)->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->get('/communities')
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->url('/communities')
                    ->has('communities.data', 3)
                    ->hasAll([
                        'communities.data.0' => 3,
                        'communities.data.0.id',
                        'communities.data.0.name',
                        'communities.data.0.slug'
                    ])
                    ->missing('communities.data.0.description')
            );
    }

    /** @test */
    public function userWhoDoesNotOwnCommunitiesCannotSeeThem()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        Community::factory(10)->for($user)->create();

        Sanctum::actingAs($user2, ['*']);

        $this->get('/communities')
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->has('communities.data', 0)
                    ->missingAll([
                        'communities.data.0.id',
                        'communities.data.0.name',
                        'communities.data.0.slug'
                    ])
            );
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
