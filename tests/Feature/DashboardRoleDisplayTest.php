<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_manage_and_generate_actions_on_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Gerer les championnats');
        $response->assertSee('Gerer les equipes');
        $response->assertSee('Generer les matchs');
        $response->assertDontSee('Voir les matchs');
    }

    public function test_visitor_sees_view_actions_on_dashboard(): void
    {
        $visitor = User::factory()->create([
            'role' => 'visiteur',
        ]);

        $response = $this->actingAs($visitor)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Voir les championnats');
        $response->assertSee('Voir les equipes');
        $response->assertSee('Voir les matchs');
        $response->assertDontSee('Generer les matchs');
    }
}

