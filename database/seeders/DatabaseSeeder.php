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

        // Inseriamo 4 rarità
        $rarities = [
            ['name' => 'Common', 'hexColor' => '#A0A0A0', 'multiplier' => 1, 'levelRequiredToDrop' => 1],
            ['name' => 'Rare', 'hexColor' => '#0070DD', 'multiplier' => 2, 'levelRequiredToDrop' => 10],
            ['name' => 'Epic', 'hexColor' => '#A335EE', 'multiplier' => 4, 'levelRequiredToDrop' => 25],
            ['name' => 'Legendary', 'hexColor' => '#FFD700', 'multiplier' => 10, 'levelRequiredToDrop' => 50],
        ];

        DB::table('rarities')->insert($rarities);

        // Prendiamo gli ID appena inseriti
        $rarityIds = DB::table('rarities')->pluck('id')->toArray();

        // Equippabili fighi
        $equippables = [
            [
                'name' => 'Steel Sword',
                'description' => 'A reliable sword made of steel.',
                'type' => 1,
                'requiredLevel' => 1,
                'baseDamage' => 15,
                'baseHealth' => 0,
                'imagePath' => 'axe'
            ],
            [
                'name' => 'Axe of Fury',
                'description' => 'Ancient axe with a furious edge.',
                'type' => 1,
                'requiredLevel' => 18,
                'baseDamage' => 5,
                'baseHealth' => 5,
                'imagePath' => 'sword'
            ],
            [
                'name' => "Viking's Helmet",
                'description' => 'A helmet that belonged to a Viking warrior.',
                'type' => 2,
                'requiredLevel' => 1,
                'baseDamage' => 5,
                'baseHealth' => 10,
                'imagePath' => 'helmet'
            ],
            [
                'name' => "Knight's Helmet",
                'description' => 'A helmet that belonged to a Captain.',
                'type' => 2,
                'requiredLevel' => 5,
                'baseDamage' => 7,
                'baseHealth' => 7,
                'imagePath' => 'helmet2'
            ],
            [
                'name' => 'Rune of Power',
                'description' => 'A rune that grants immense power.',
                'type' => 3,
                'requiredLevel' => 5,
                'baseDamage' => 100,
                'baseHealth' => 10,
                'imagePath' => 'rune__1_'
            ],
            [
                'name' => 'Rune of Swiftness',
                'description' => 'A rune that grants incredible speed.',
                'type' => 3,
                'requiredLevel' => 15,
                'baseDamage' => 10,
                'baseHealth' => 100,
                'imagePath' => 'rune__2_'
            ]

        ];

        DB::table('equippableItemBlueprints')->insert($equippables);

        // Oggetti utilizzabili per le battaglie
        $usableItems = [
            [
                'name' => 'Healing Potion',
                'description' => 'Restores 50 health instantly.',
                'healthRecovery' => 50,
                'rarityId' => $rarityIds[0],
                'imagePath' => 'lifePotion'
            ]
        ];

        DB::table('usableItems')->insert($usableItems);
        
    }
}
