<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Align with saashmagna.sql production database structure:
     * - Rename 'name' to 'value'
     * - Remove 'description' column
     * - Make color columns NOT NULL
     */
    public function up(): void
    {
        Schema::table('block_inspection_values', function (Blueprint $table) {
            // Drop the description column (doesn't exist in production)
            $table->dropColumn('description');
        });

        // Rename 'name' to 'value' to match production
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->renameColumn('name', 'value');
        });

        // Modify color columns to be NOT NULL (matching production)
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

        // Rename 'value' back to 'name'
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->renameColumn('value', 'name');
        });

        // Restore description column
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->string('description', 255)->nullable()->after('value');
        });
    }
};
