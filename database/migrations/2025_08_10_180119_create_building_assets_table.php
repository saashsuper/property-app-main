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
        Schema::create('building_assets', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->string('name', 50);
            $table->unsignedSmallInteger('block_inspection_value_type_id')->default(1);
            $table->timestamps();

            $table->foreign('block_inspection_value_type_id')->references('id')->on('block_inspection_value_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_assets');
    }
};
