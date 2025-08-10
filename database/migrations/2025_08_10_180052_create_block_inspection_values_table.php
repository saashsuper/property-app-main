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
        Schema::create('block_inspection_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('block_inspection_value_type_id');
            $table->string('name', 50);
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->foreign('block_inspection_value_type_id')->references('id')->on('block_inspection_value_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('block_inspection_values');
    }
};
