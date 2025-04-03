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
        Schema::create('usableItems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->foreignId('rarityId')
                ->constrained('rarities');
            $table->integer('healthRecovery');
            $table->integer('damageAplifier');
            $table->integer('durationInSeconds');
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
        Schema::dropIfExists('usableItems');
    }
};
