<?php

use App\Models\Enums\EquippableItemType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("equippableItemBlueprints",function(Blueprint $table){
            $table->id();
            $table->string("name");
            $table->string("description");
            $table->integer("baseDamage");
            $table->integer("baseHealth");
            $table->integer("requiredLevel");
            $table->float("randomFactor")->default(rand(0,2));
            // enum 'weapon', 'armor', 'rune' in Models/EquippableItemType.php
            $table->enum('type', [EquippableItemType::WEAPON, EquippableItemType::ARMOR, EquippableItemType::RUNE]);
            
        });

        Schema::create('equippableItems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blueprintId')
                ->constrained('equippableItemBlueprints');
            $table->foreignId('rarityId')
                ->constrained('rarities');
            $table->integer('requiredLevel');
            $table->foreignId('ownerId')
                ->constrained('players');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equippableItemBlueprints');
        Schema::dropIfExists('equippableItems');
    }
};
