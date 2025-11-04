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
     * - 2025_08_10_180200_create_block_inspection_assets_table.php (initial table creation)
     * - 2025_10_18_100000_update_block_inspection_assets_for_general_assets.php
     * - 2025_10_18_110000_make_comments_nullable_in_block_inspection_assets.php
     * - 2025_11_02_095917_add_additional_comments_to_block_inspection_assets_table.php
     * - 2025_11_02_101428_make_block_inspection_assets_nullable_fields.php
     * 
     * Final structure supports:
     * - Building-specific assets (block_building_id + building_asset_id)
     * - General assets (block_general_asset_id)
     * - Flexible inspections (all asset/value fields nullable)
     * - Additional comments for Commercial/Houses building types
     */
    public function up(): void
    {
        Schema::create('block_inspection_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_inspection_id');
            $table->unsignedBigInteger('block_building_id')->nullable();
            $table->unsignedSmallInteger('building_asset_id')->nullable();
            $table->unsignedSmallInteger('block_general_asset_id')->nullable();
            $table->unsignedBigInteger('block_inspection_value_id')->nullable();
            $table->string('comments', 255)->nullable();
            $table->text('additional_comments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('block_inspection_id')->references('id')->on('block_inspections')->onDelete('cascade');
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
            $table->foreign('building_asset_id')->references('id')->on('building_assets')->onDelete('cascade');
            // Note: block_general_asset_id foreign key will be added after block_general_assets table is created
            $table->foreign('block_inspection_value_id')->references('id')->on('block_inspection_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspection_assets');
    }
};

