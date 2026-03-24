<?php

namespace Tests\Feature;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\Fixture;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_generate_round_robin_matches(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();

        Equipe::factory()->count(3)->create([
            'championnat_id' => $championnat->id,
        ]);

        $response = $this->actingAs($admin)->post(route('matchs.generer'), [
            'championnat_id' => $championnat->id,
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $this->assertDatabaseCount('matchs', 3);
    }

    public function test_generating_matches_requires_at_least_two_teams(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();

        Equipe::factory()->create([
            'championnat_id' => $championnat->id,
        ]);

        $response = $this->actingAs($admin)->post(route('matchs.generer'), [
            'championnat_id' => $championnat->id,
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $response->assertSessionHas('error');
        $this->assertDatabaseCount('matchs', 0);
    }

    public function test_visitor_cannot_generate_matches(): void
    {
        $visitor = User::factory()->create(['role' => 'visiteur']);
        $championnat = Championnat::factory()->create();

        $response = $this->actingAs($visitor)->post(route('matchs.generer'), [
            'championnat_id' => $championnat->id,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_simulate_scores_for_existing_matches(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => null,
            'score2' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('matchs.simuler'), [
            'championnat_id' => $championnat->id,
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));

        $fixture = Fixture::firstOrFail();
        $this->assertNotNull($fixture->score1);
        $this->assertNotNull($fixture->score2);
        $this->assertGreaterThanOrEqual(0, $fixture->score1);
        $this->assertGreaterThanOrEqual(0, $fixture->score2);
        $this->assertLessThanOrEqual(5, $fixture->score1);
        $this->assertLessThanOrEqual(5, $fixture->score2);
    }

    public function test_admin_can_update_a_match_score(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        $fixture = Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => null,
            'score2' => null,
        ]);

        $response = $this->actingAs($admin)->patch(route('matchs.score', $fixture), [
            'match_id' => $fixture->id,
            'maison_id' => $team1->id,
            'exterieur_id' => $team2->id,
            'score1' => 4,
            'score2' => 2,
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $this->assertDatabaseHas('matchs', [
            'id' => $fixture->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => 4,
            'score2' => 2,
        ]);
    }

    public function test_admin_can_swap_home_and_away_when_updating_score(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        $fixture = Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => null,
            'score2' => null,
        ]);

        $response = $this->actingAs($admin)->patch(route('matchs.score', $fixture), [
            'match_id' => $fixture->id,
            'maison_id' => $team2->id,
            'exterieur_id' => $team1->id,
            'score1' => 1,
            'score2' => 0,
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $this->assertDatabaseHas('matchs', [
            'id' => $fixture->id,
            'equipe1_id' => $team2->id,
            'equipe2_id' => $team1->id,
            'score1' => 1,
            'score2' => 0,
        ]);
    }

    public function test_admin_cannot_set_score_above_fifty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        $fixture = Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => null,
            'score2' => null,
        ]);

        $response = $this->actingAs($admin)
            ->from(route('matchs.index', ['championnat_id' => $championnat->id]))
            ->patch(route('matchs.score', $fixture), [
                'match_id' => $fixture->id,
                'maison_id' => $team1->id,
                'exterieur_id' => $team2->id,
                'score1' => 51,
                'score2' => 2,
            ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $response->assertSessionHasErrors('score1');
        $this->assertDatabaseHas('matchs', [
            'id' => $fixture->id,
            'score1' => null,
            'score2' => null,
        ]);
    }

    public function test_visitor_cannot_update_a_match_score(): void
    {
        $visitor = User::factory()->create(['role' => 'visiteur']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        $fixture = Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => null,
            'score2' => null,
        ]);

        $response = $this->actingAs($visitor)->patch(route('matchs.score', $fixture), [
            'match_id' => $fixture->id,
            'maison_id' => $team1->id,
            'exterieur_id' => $team2->id,
            'score1' => 1,
            'score2' => 0,
        ]);

        $response->assertForbidden();
    }

    public function test_visitor_on_match_page_cannot_see_scores(): void
    {
        $visitor = User::factory()->create(['role' => 'visiteur']);
        $championnat = Championnat::factory()->create(['nom' => 'Premier League']);

        $team1 = Equipe::factory()->create([
            'nom' => 'Arsenal',
            'championnat_id' => $championnat->id,
        ]);
        $team2 = Equipe::factory()->create([
            'nom' => 'Chelsea',
            'championnat_id' => $championnat->id,
        ]);

        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => 2,
            'score2' => 1,
        ]);

        $response = $this->actingAs($visitor)->get(route('matchs.index', [
            'championnat_id' => $championnat->id,
        ]));

        $response->assertOk();
        $response->assertSee('Arsenal');
        $response->assertSee('Chelsea');
        $response->assertSee('Maison');
        $response->assertSee('Exterieur');
        $response->assertDontSee('Score');
        $response->assertDontSee('2 - 1');
        $response->assertDontSee('Saisie');
    }

    public function test_admin_can_reset_only_scores(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        $fixture = Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => 3,
            'score2' => 3,
        ]);

        $response = $this->actingAs($admin)->post(route('matchs.reset'), [
            'championnat_id' => $championnat->id,
            'mode' => 'scores',
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $this->assertDatabaseHas('matchs', [
            'id' => $fixture->id,
            'score1' => null,
            'score2' => null,
        ]);
    }

    public function test_admin_can_reset_matches(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $championnat = Championnat::factory()->create();
        [$team1, $team2] = Equipe::factory()->count(2)->create([
            'championnat_id' => $championnat->id,
        ]);

        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $team1->id,
            'equipe2_id' => $team2->id,
            'score1' => 1,
            'score2' => 0,
        ]);

        $response = $this->actingAs($admin)->post(route('matchs.reset'), [
            'championnat_id' => $championnat->id,
            'mode' => 'matchs',
        ]);

        $response->assertRedirect(route('matchs.index', ['championnat_id' => $championnat->id]));
        $this->assertDatabaseCount('matchs', 0);
    }

    public function test_classement_is_computed_and_sorted_by_points_then_goal_difference(): void
    {
        $user = User::factory()->create();
        $championnat = Championnat::factory()->create(['nom' => 'Classement Test']);

        $teamA = Equipe::factory()->create(['nom' => 'Team A', 'championnat_id' => $championnat->id]);
        $teamB = Equipe::factory()->create(['nom' => 'Team B', 'championnat_id' => $championnat->id]);
        $teamC = Equipe::factory()->create(['nom' => 'Team C', 'championnat_id' => $championnat->id]);

        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $teamA->id,
            'equipe2_id' => $teamB->id,
            'score1' => 2,
            'score2' => 0,
        ]);
        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $teamA->id,
            'equipe2_id' => $teamC->id,
            'score1' => 0,
            'score2' => 1,
        ]);
        Fixture::create([
            'championnat_id' => $championnat->id,
            'equipe1_id' => $teamB->id,
            'equipe2_id' => $teamC->id,
            'score1' => 1,
            'score2' => 1,
        ]);

        $response = $this->actingAs($user)->get(route('matchs.classement', [
            'championnat_id' => $championnat->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('classement', function ($classement) {
            $orderedNames = $classement->pluck('nom')->all();

            return $orderedNames === ['Team C', 'Team A', 'Team B'];
        });
    }
}
