<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Makes certain fields nullable in block_inspection_assets table to support:
     * - General assets (no building_id)
     * - Commercial/Houses observations (no building_asset_id, no value_id)
     * - Flexible inspection data entry
     */
    public function up(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Make these fields nullable
            $table->unsignedBigInteger('block_building_id')->nullable()->change();
            $table->unsignedSmallInteger('building_asset_id')->nullable()->change();
            $table->unsignedBigInteger('block_inspection_value_id')->nullable()->change();
            $table->string('comments', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Revert to NOT NULL (may fail if NULL values exist)
            $table->unsignedBigInteger('block_building_id')->nullable(false)->change();
            $table->unsignedSmallInteger('building_asset_id')->nullable(false)->change();
            $table->unsignedBigInteger('block_inspection_value_id')->nullable(false)->change();
            $table->string('comments', 255)->nullable(false)->change();
        });
    }
};
