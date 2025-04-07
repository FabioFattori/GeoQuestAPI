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
        Schema::create('usableItems_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usableItemId')->constrained("usableItems")->onDelete('cascade');
            $table->foreignId('ownerId')->constrained("players")->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("usableItems_users");
    }
};
