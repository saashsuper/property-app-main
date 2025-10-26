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
        Schema::create('block_building_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('block_id');
            $table->unsignedSmallInteger('building_asset_id');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('building_asset_id')->references('id')->on('building_assets')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate entries
            $table->unique(['block_id', 'building_asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_building_assets');
    }
};
