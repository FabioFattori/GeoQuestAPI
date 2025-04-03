<?php

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
        Schema::create('rarities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string("hexColor");
            $table->float('multiplier')->default(1);
            $table->timestamps();
        });

        Schema::create('players',function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('level')->default(1);
            // rappresents the experience that the players collected
            // the experience needed will be calculated by the model Player
            $table->integer('experienceCollected')->default(0);
            $table->integer("nWonBattles")->default(0);
            $table->integer("nBattles")->default(0);
            $table->integer("currentHealth")->default(15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rarities');
        Schema::dropIfExists('players');
    }
};
