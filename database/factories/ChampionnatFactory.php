<?php

namespace Database\Factories;

use App\Models\Championnat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Championnat>
 */
class ChampionnatFactory extends Factory
{
    protected $model = Championnat::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->words(2, true),
        ];
    }
}
