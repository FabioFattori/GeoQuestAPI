<?php

namespace Database\Seeders;

use App\Models\EquippableItem;
use App\Models\EquippableItemBlueprint;
use App\Models\Player;
use App\Models\Rarity;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use DB;
use Illuminate\Database\Seeder;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // use the factories

        for ($i = 0; $i < 10; $i++) {
            DB::table("players")->insert([
                "name" => Str::random(),
                "level" => random_int(1, 100),
                "experienceCollected" => random_int(0, 10000),
                "nWonBattles" => random_int(0, 100),
                "nBattles" => random_int(0, 100),
                "helmetId" => null,
                "runeId" => null,
                "weaponId" => null,
                "currentHealth" => random_int(1, 100),
            ]);

            DB::table("users")->insert([
                "email" => Str::random(),
                "password" => Str::random(),
                "playerId" => Player::all()->last()->id
            ]);

            DB::table("rarities")->insert([
                "name" => Str::random(),
                "hexColor" => Str::random(),
                "multiplier" => random_int(1, 10),
                "levelRequiredToDrop" => random_int(1, 100)
            ]);

            DB::table("equippableItemBlueprints")->insert([
                "name" => Str::random(),
                "description" => Str::random(),
                "type" => random_int(1, 3),
                "requiredLevel" => random_int(1, 100),
                "baseDamage" => random_int(1, 100),
                "baseHealth" => random_int(1, 100),
                "imagePath" => Str::random()
            ]);

            DB::table("usableItems")->insert([
                "name" => Str::random(),
                'description' => Str::random(),
                'durationInSeconds' => random_int(0, 100),
                'healthRecovery' => random_int(0, 100),
                'damageAplifier' => random_int(1, 2),
                "rarityId" => Rarity::inRandomOrder()->first()->id
            ]);
        }
    }
}
