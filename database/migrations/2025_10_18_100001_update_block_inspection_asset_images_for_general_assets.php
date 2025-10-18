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
        Schema::table('block_inspection_asset_images', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['block_building_id']);
            
            // Make block_building_id nullable and remove default value
            $table->unsignedBigInteger('block_building_id')->nullable()->change();
            
            // Re-add the foreign key constraint
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_asset_images', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['block_building_id']);
            
            // Revert to non-nullable with default value
            $table->unsignedBigInteger('block_building_id')->default(0)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
        });
    }
};

