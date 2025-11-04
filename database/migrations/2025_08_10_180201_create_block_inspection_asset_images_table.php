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
     * - 2025_08_10_180201_create_block_inspection_asset_images_table.php (initial table creation)
     * - 2025_10_18_100001_update_block_inspection_asset_images_for_general_assets.php
     * - 2025_10_25_000002_add_gps_to_work_order_images_table.php (GPS fields only)
     */
    public function up(): void
    {
        Schema::create('block_inspection_asset_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_inspection_asset_id');
            $table->unsignedBigInteger('block_inspection_id');
            $table->unsignedBigInteger('block_building_id')->nullable();
            $table->unsignedSmallInteger('building_asset_id')->nullable();
            $table->string('image_path', 255)->nullable();
            $table->string('image_name', 100)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedSmallInteger('s3_status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('block_inspection_asset_id')->references('id')->on('block_inspection_assets')->onDelete('cascade');
            $table->foreign('block_inspection_id')->references('id')->on('block_inspections')->onDelete('cascade');
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
            $table->foreign('building_asset_id')->references('id')->on('building_assets')->onDelete('cascade');

            // Indexes for better performance
            $table->index('block_inspection_asset_id');
            $table->index('block_inspection_id');
            $table->index('block_building_id');
            $table->index('building_asset_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspection_asset_images');
    }
};

