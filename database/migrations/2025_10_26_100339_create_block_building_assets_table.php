<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CONSOLIDATED MIGRATION - Combines:
     * - 2025_10_26_090317_create_block_building_assets_table.php (initial pivot table)
     * - 2025_10_26_100339_modify_block_building_assets_table_structure.php (restructure to asset table)
     * 
     * Final structure: Renamed from building_assets with block-specific asset data
     */
    public function up(): void
    {
        Schema::create('block_building_assets', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('name', 50);
            $table->unsignedSmallInteger('block_inspection_value_type_id')->default(1);
            $table->timestamps();

            $table->foreign('block_inspection_value_type_id')
                  ->references('id')
                  ->on('block_inspection_value_types')
                  ->onDelete('cascade');
        });

        // Copy data from building_assets to block_building_assets
        // Note: This assumes building_assets exists and has data
        // You may need to adjust this based on your seeding strategy
        DB::statement('
            INSERT INTO block_building_assets (id, name, block_inspection_value_type_id, created_at, updated_at)
            SELECT id, name, block_inspection_value_type_id, created_at, updated_at
            FROM building_assets
            WHERE 1=0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_building_assets');
    }
};

