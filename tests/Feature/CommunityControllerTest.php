<?php

namespace Tests\Feature;

use App\Models\Comment;
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
        $this->get('/communities')
            ->assertRedirect('/login');
    }

    /** @test */
    public function onlyAuthenticateUserCanVisitIndexPage()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->get('/communities')
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
        $this->get('/communities/create')
            ->assertRedirect('/login');
    }

    /** @test */
    public function onlyAuthenticateUserCanVisitCreatePage()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['*']);

        $this->get('/communities/create')
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

    /** @test */
    public function unauthenticateUserCannotVisitEditPage()
    {
        $community = Community::factory()->create();

        $this->get(route('communities.edit', $community->slug))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCannotVisitEditPageIfCommunityDoesNotBelongToHim()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user2, ['*']);

        $this->get(route('communities.edit', $community->slug))
            ->assertForbidden();
    }

    /** @test */
    public function authenticateUserCanVisitEditPageIfCommunityBelongsToHim()
    {
        $user = User::factory()->create();
        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->get(route('communities.edit', $community->slug))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->whereAll([
                    'community.user_id' => $user->id, 
                    'community.id' => $community->id,
                    'community.name' => $community->name,
                    'community.description' => $community->description
                ])
        );
    }

    /** @test */
    public function unauthenticateUserCannotUpdateCommunity()
    {
        $community = Community::factory()->create();

        $this->put('/communities/' . $community->slug, Community::factory()->raw())
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCannotUpdateCommunityIfDoesNotBelongToHim()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user2, ['*']);

        $this->put('/communities/' . $community->slug, Community::factory()->raw())
            ->assertForbidden();

        $this->assertDatabaseHas('communities', [
            'name' => $community->name,
            'description' => $community->description
        ]);
    }

    /** @test */
    public function validationWorksForCommunityUpdate()
    {
        $user = User::factory()->create();

        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->put('/communities/' . $community->slug, [])
            ->assertSessionHasErrors('name', 'description');
    }

    /** @test */
    public function authenticateUserCanUpdateCommunityIfBelongsToHim()
    {   
        $user = User::factory()->create();

        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->put('/communities/' . $community->slug, [
            'name' => 'Updated name',
            'description' => 'Updated description'
        ])
        ->assertSessionDoesntHaveErrors()
        ->assertRedirect(route('communities.index'));
        
        $this->assertDatabaseHas('communities', [
            'name' => 'Updated name',
            'description' => 'Updated description'
        ]);
    }

    /** @test */
    public function unauthenticateUserCannotDeleteCommynity()
    {
        $community = Community::factory()->create();

        $this->delete(route('communities.destroy', $community->slug))
            ->assertRedirect('/login');
    }

    /** @test */
    public function authenticateUserCannotDeleteCommunityIfDoesNotBelongToHim()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user2, ['*']);

        $this->delete(route('communities.destroy', $community->slug))
            ->assertForbidden();

        $this->assertModelExists($community);
    }

    /** @test */
    public function authenticateUserCanDeleteCommunityIfBelongsToHim()
    {
        $user = User::factory()->create();
        $community = Community::factory()->for($user)->create();

        Sanctum::actingAs($user, ['*']);

        $this->delete(route('communities.destroy', $community->slug))
            ->assertRedirect();

        $this->assertModelMissing($community);
    }
}
