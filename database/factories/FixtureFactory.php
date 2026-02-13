<?php

namespace Database\Factories;

use App\Models\Championnat;
use App\Models\Equipe;
use App\Models\Fixture;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Fixture>
 */
class FixtureFactory extends Factory
{
    protected $model = Fixture::class;

    public function definition(): array
    {
        $championnat = Championnat::factory();

        return [
            'championnat_id' => $championnat,
            'equipe1_id' => Equipe::factory()->for($championnat, 'championnat'),
            'equipe2_id' => Equipe::factory()->for($championnat, 'championnat'),
            'score1' => $this->faker->numberBetween(0, 5),
            'score2' => $this->faker->numberBetween(0, 5),
        ];
    }

    public function pending(): self
    {
        return $this->state(fn () => [
            'score1' => null,
            'score2' => null,
        ]);
    }
}
