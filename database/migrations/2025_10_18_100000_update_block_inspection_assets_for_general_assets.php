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
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Make block_building_id and building_asset_id nullable
            $table->unsignedBigInteger('block_building_id')->nullable()->change();
            $table->unsignedSmallInteger('building_asset_id')->nullable()->change();
            
            // Add block_general_asset_id field (nullable)
            $table->unsignedSmallInteger('block_general_asset_id')->nullable()->after('building_asset_id');
            
            // Add foreign key for general assets
            $table->foreign('block_general_asset_id')->references('id')->on('block_general_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['block_general_asset_id']);
            
            // Drop the column
            $table->dropColumn('block_general_asset_id');
            
            // Revert nullable changes (note: this might fail if there are null values)
            $table->unsignedBigInteger('block_building_id')->nullable(false)->change();
            $table->unsignedSmallInteger('building_asset_id')->nullable(false)->change();
        });
    }
};

