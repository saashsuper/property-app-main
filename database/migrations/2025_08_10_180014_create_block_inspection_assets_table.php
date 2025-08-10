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
        Schema::create('block_inspection_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('block_inspection_id');
            $table->unsignedBigInteger('block_building_id');
            $table->unsignedSmallInteger('building_asset_id');
            $table->unsignedInteger('block_inspection_value_id');
            $table->string('comments', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('block_inspection_id')->references('id')->on('block_inspections')->onDelete('cascade');
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
            $table->foreign('building_asset_id')->references('id')->on('building_assets')->onDelete('cascade');
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
