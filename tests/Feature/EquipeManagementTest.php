<?php

namespace Tests\Feature;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EquipeManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_route_displays_teams(): void
    {
        $user = User::factory()->create();
        $championnat = Championnat::factory()->create(['nom' => 'Ligue 1']);
        Equipe::factory()->create([
            'nom' => 'Paris FC',
            'championnat_id' => $championnat->id,
        ]);

        $response = $this->actingAs($user)->get(route('equipes.index'));

        $response->assertOk();
        $response->assertSee('Paris FC');
        $response->assertSee('Ligue 1');
    }

    public function test_admin_can_create_update_and_delete_team(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();

        $createResponse = $this->actingAs($admin)->post(route('equipes.store'), [
            'nom' => 'Nice',
            'championnat_id' => $championnat->id,
        ]);

        $createResponse->assertRedirect(route('equipes.index'));
        $this->assertDatabaseHas('equipes', [
            'nom' => 'Nice',
            'championnat_id' => $championnat->id,
        ]);

        $team = Equipe::where('nom', 'Nice')->firstOrFail();

        $updateResponse = $this->actingAs($admin)->patch(route('equipes.update', $team), [
            'nom' => 'Nice FC',
            'championnat_id' => $championnat->id,
        ]);

        $updateResponse->assertRedirect(route('equipes.index'));
        $this->assertDatabaseHas('equipes', [
            'id' => $team->id,
            'nom' => 'Nice FC',
        ]);

        $deleteResponse = $this->actingAs($admin)->delete(route('equipes.destroy', $team));

        $deleteResponse->assertRedirect(route('equipes.index'));
        $this->assertDatabaseMissing('equipes', ['id' => $team->id]);
    }

    public function test_visitor_cannot_manage_teams(): void
    {
        $visitor = User::factory()->create(['role' => 'visiteur']);
        $championnat = Championnat::factory()->create();
        $team = Equipe::factory()->create(['championnat_id' => $championnat->id]);

        $createResponse = $this->actingAs($visitor)->post(route('equipes.store'), [
            'nom' => 'Blocked Team',
            'championnat_id' => $championnat->id,
        ]);
        $createResponse->assertForbidden();

        $updateResponse = $this->actingAs($visitor)->patch(route('equipes.update', $team), [
            'nom' => 'Blocked Update',
            'championnat_id' => $championnat->id,
        ]);
        $updateResponse->assertForbidden();

        $deleteResponse = $this->actingAs($visitor)->delete(route('equipes.destroy', $team));
        $deleteResponse->assertForbidden();
    }
}

