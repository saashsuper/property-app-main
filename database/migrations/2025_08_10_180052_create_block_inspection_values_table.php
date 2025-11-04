<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CONSOLIDATED MIGRATION - Combines:
     * - 2025_08_10_180052_create_block_inspection_values_table.php (initial table creation)
     * - 2025_10_26_105844_add_color_columns_to_block_inspection_values_table.php
     * - 2025_10_26_111008_align_block_inspection_values_with_production.php
     * - 2025_10_26_151755_rename_value_to_name_in_block_inspection_values.php (duplicate)
     * - 2025_10_26_152157_rename_value_to_name_in_block_inspection_values_table.php (duplicate)
     * 
     * Final structure:
     * - Uses 'name' column (not 'value')
     * - Includes color and bg_color columns (NOT NULL)
     * - No description column (removed in production alignment)
     */
    public function up(): void
    {
        Schema::create('block_inspection_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('block_inspection_value_type_id');
            $table->string('name', 50);
            $table->string('color', 50)->default('btn-outline-secondary');
            $table->string('bg_color', 30)->default('grays');
            $table->timestamps();

            $table->foreign('block_inspection_value_type_id')->references('id')->on('block_inspection_value_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspection_values');
    }
};

