<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Align with requirements:
     * - Remove 'description' column
     * - Make color columns NOT NULL
     * - Keep column as 'name' (not 'value')
     */
    public function up(): void
    {
        Schema::table('block_inspection_values', function (Blueprint $table) {
            // Drop the description column (not needed)
            $table->dropColumn('description');
        });

        // Modify color columns to be NOT NULL
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->string('color', 50)->nullable(false)->change();
            $table->string('bg_color', 30)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore color columns to nullable
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->string('color', 50)->nullable()->change();
            $table->string('bg_color', 30)->nullable()->change();
        });

        // Restore description column
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('name');
        });
    }
};
