<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates block_building_type_assets table to map building types to building assets.
     * This defines which assets are relevant for each building type.
     * Migrated from building_type_assets table in saashmagna.sql
     */
    public function up(): void
    {
        Schema::create('block_building_type_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('block_building_type_id');
            $table->unsignedSmallInteger('block_building_asset_id');
            $table->timestamps();
            
            // Foreign key only for block_building_asset_id
            // Note: block_building_type_id = 0 means "general/all types", so no FK constraint
            $table->foreign('block_building_asset_id')
                  ->references('id')
                  ->on('block_building_assets')
                  ->onDelete('cascade');
                  
            // Unique constraint to prevent duplicate mappings
            $table->unique(['block_building_type_id', 'block_building_asset_id'], 'unique_building_type_asset');
            
            // Index for building_type_id for better query performance
            $table->index('block_building_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_building_type_assets');
    }
};
