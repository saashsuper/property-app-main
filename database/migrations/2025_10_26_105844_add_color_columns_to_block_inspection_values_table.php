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
        Schema::table('block_inspection_values', function (Blueprint $table) {
            // Add color and bg_color columns to match saashmagna.sql structure
            $table->string('color', 50)->default('btn-outline-secondary')->after('name');
            $table->string('bg_color', 30)->default('grays')->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->dropColumn(['color', 'bg_color']);
        });
    }
};
