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
        Schema::create('block_buildings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('block_id');
            $table->string('name', 50);
            $table->unsignedSmallInteger('building_type_id');
            $table->unsignedSmallInteger('floor_no');
            $table->string('roof_type', 50);
            $table->unsignedSmallInteger('no_lift');
            $table->string('image_path', 255)->nullable();
            $table->string('image_name', 255)->nullable();
            $table->mediumInteger('created_by')->nullable();
            $table->mediumInteger('updated_by')->nullable();
            $table->mediumInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('block_id')->references('id')->on('blocks')->onDelete('cascade');
            $table->foreign('building_type_id')->references('id')->on('building_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_buildings');
    }
};
