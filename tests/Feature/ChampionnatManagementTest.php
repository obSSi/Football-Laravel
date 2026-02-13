<?php

namespace Tests\Feature;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class ChampionnatManagementTest
 *
 * This test suite exercises the main championship management actions:
 * listing, creation, validation, update, and deletion.
 */
class ChampionnatManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test championship listing.
     *
     * @return void
     */
    public function testIndexRouteDisplaysChampionnats(): void
    {
        /** @var User $admin Administrator used for authenticated routes */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var Championnat $championnat Championship with linked teams */
        $championnat = Championnat::factory()->create([
            'nom' => 'Ligue 1',
        ]);

        Equipe::factory()
            ->count(2)
            ->for($championnat)
            ->create();

        /** @var \Illuminate\Testing\TestResponse $response Call to the index route */
        $response = $this->actingAs($admin)->get(route('championnats.index'));

        // Assertions
        $response->assertOk();
        $response->assertSee('Ligue 1');
        $response->assertViewHas('championnats', function ($championnats) use ($championnat) {
            return $championnats->contains(fn ($item) => $item->id === $championnat->id && $item->equipes_count === 2);
        });
    }

    /**
     * Test championship creation with valid data.
     *
     * @return void
     */
    public function testStoreCreatesChampionnat(): void
    {
        /** @var User $admin Administrator used for authenticated routes */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var array<string, string> $data Championship payload */
        $data = [
            'nom' => 'Premier League',
        ];

        /** @var \Illuminate\Testing\TestResponse $response POST request */
        $response = $this->actingAs($admin)->post(route('championnats.store'), $data);

        // Assertions
        $response->assertRedirect(route('championnats.index'));
        $this->assertDatabaseHas('championnats', $data);
    }

    /**
     * Test validation errors when creating a championship with invalid data.
     *
     * @return void
     */
    public function testStoreFailsWithInvalidData(): void
    {
        /** @var User $admin Administrator used for authenticated routes */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var array<string, string> $data Invalid championship payload */
        $data = [
            'nom' => 'A',
        ];

        /** @var \Illuminate\Testing\TestResponse $response POST request */
        $response = $this->actingAs($admin)->post(route('championnats.store'), $data);

        // Assertions
        $response->assertSessionHasErrors(['nom']);
        $this->assertDatabaseCount('championnats', 0);
    }

    /**
     * Test championship update.
     *
     * @return void
     */
    public function testUpdateEditsChampionnat(): void
    {
        /** @var User $admin Administrator used for authenticated routes */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var Championnat $championnat Existing championship */
        $championnat = Championnat::factory()->create([
            'nom' => 'Serie A',
        ]);

        /** @var array<string, string> $updatedData Update payload */
        $updatedData = [
            'nom' => 'Serie A TIM',
        ];

        /** @var \Illuminate\Testing\TestResponse $response PATCH request */
        $response = $this->actingAs($admin)->patch(route('championnats.update', $championnat), $updatedData);

        // Assertions
        $response->assertRedirect(route('championnats.index'));
        $this->assertDatabaseHas('championnats', [
            'id' => $championnat->id,
            'nom' => 'Serie A TIM',
        ]);
    }

    /**
     * Test championship deletion.
     *
     * @return void
     */
    public function testDestroyDeletesChampionnat(): void
    {
        /** @var User $admin Administrator used for authenticated routes */
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        /** @var Championnat $championnat Championship slated for deletion */
        $championnat = Championnat::factory()->create();

        /** @var \Illuminate\Testing\TestResponse $response DELETE request */
        $response = $this->actingAs($admin)->delete(route('championnats.destroy', $championnat));

        // Assertions
        $response->assertRedirect(route('championnats.index'));
        $this->assertDatabaseMissing('championnats', ['id' => $championnat->id]);
    }
}
