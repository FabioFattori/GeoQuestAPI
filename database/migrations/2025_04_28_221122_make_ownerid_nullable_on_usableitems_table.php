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
        Schema::table('usableItems', function (Blueprint $table) {
            $table->foreignId('ownerId')->nullable()->change();
        });
        Schema::table('equippableItems', function (Blueprint $table) {
            $table->foreignId('ownerId')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('usableItems', function (Blueprint $table) {
            $table->foreignId('ownerId')->nullable(false)->change();
        });
        Schema::table('equippableItems', function (Blueprint $table) {
            $table->foreignId('ownerId')->nullable()->change();
        });
    }
};
