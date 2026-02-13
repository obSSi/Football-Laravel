<?php

namespace Database\Factories;

use App\Models\Championnat;
use App\Models\Equipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Equipe>
 */
class EquipeFactory extends Factory
{
    protected $model = Equipe::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->city(),
            'championnat_id' => Championnat::factory(),
        ];
    }
}
