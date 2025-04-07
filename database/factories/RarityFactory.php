<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rarity>
 */
class RarityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'hexColor' => $this->faker->hexColor(),
            'multiplier' => $this->faker->randomFloat(2, 1, 10),
            'levelRequiredToDrop' => $this->faker->numberBetween(1, 100),
        ];
    }
}
