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
        Schema::create('league', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('position')->nullable();
            $table->unsignedBigInteger('playerId')->nullable();
            $table->foreign('playerId')
                ->references('id')
                ->on('players')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league');
    }
};
