<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing block_building_assets table
        Schema::dropIfExists('block_building_assets');

        // Create block_building_assets table with same structure as building_assets
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
        DB::statement('
            INSERT INTO block_building_assets (id, name, block_inspection_value_type_id, created_at, updated_at)
            SELECT id, name, block_inspection_value_type_id, created_at, updated_at
            FROM building_assets
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the new structure
        Schema::dropIfExists('block_building_assets');

        // Recreate the old pivot table structure
        Schema::create('block_building_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('block_id');
            $table->unsignedSmallInteger('building_asset_id');
            $table->timestamps();

            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('building_asset_id')->references('id')->on('building_assets')->onDelete('cascade');
            
            $table->unique(['block_id', 'building_asset_id']);
        });
    }
};
