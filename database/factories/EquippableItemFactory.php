<?php

namespace Database\Factories;

use App\Models\EquippableItemBlueprint;
use App\Models\Player;
use App\Models\Rarity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EquippableItem>
 */
class EquippableItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "ownerId" => Player::factory(),
            "blueprintId" => EquippableItemBlueprint::factory(),
            "rarityId" => Rarity::factory(),
        ];
    }

}
