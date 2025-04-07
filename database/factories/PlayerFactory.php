<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'level' => $this->faker->numberBetween(1, 100),
            'experienceCollected' => $this->faker->numberBetween(0, 10000),
            'nWonBattles' => $this->faker->numberBetween(0, 100),
            'nBattles' => $this->faker->numberBetween(0, 100),
            'helmetId' => null,
            'runeId' => null,
            'weaponId' => null,
            'currentHealth' => $this->faker->numberBetween(1, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
