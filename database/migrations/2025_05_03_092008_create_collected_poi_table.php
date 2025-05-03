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
        Schema::create('collected_poi', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('playerId')
                ->constrained('players');
            $table->float('latitude');
            $table->float('longitude');
        });

        Schema::create('completed_quest', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('playerId')
                ->constrained('players');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collected_poi');
    }
};
