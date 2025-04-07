<?php

namespace Database\Factories;

use App\Models\Enums\EquippableItemType;
use App\Models\Rarity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquippableItemBlueprint>
 */
class EquippableItemBlueprintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->word(),
            "description" => $this->faker->sentence(),
            "type" => $this->faker->randomElement([1,2,3]),
            "imagePath" => $this->faker->imageUrl(),
            "baseDamage" => $this->faker->numberBetween(1, 100),
            "baseHealth" => $this->faker->numberBetween(1, 100),
            "requiredLevel" => $this->faker->numberBetween(1, 10),
            "randomFactor" => $this->faker->numberBetween(1, 10),
        ];
    }
}
