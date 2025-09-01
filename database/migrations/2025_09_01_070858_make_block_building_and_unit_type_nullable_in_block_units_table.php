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
        Schema::table('block_units', function (Blueprint $table) {
            // First drop foreign key constraints
            $table->dropForeign(['block_building_id']);
            $table->dropForeign(['block_unit_type_id']);
            
            // Make columns nullable
            $table->unsignedBigInteger('block_building_id')->nullable()->change();
            $table->unsignedSmallInteger('block_unit_type_id')->nullable()->change();
            
            // Re-add foreign key constraints with nullable
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('set null');
            $table->foreign('block_unit_type_id')->references('id')->on('block_unit_types')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_units', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['block_building_id']);
            $table->dropForeign(['block_unit_type_id']);
            
            // Make columns non-nullable again
            $table->unsignedBigInteger('block_building_id')->nullable(false)->change();
            $table->unsignedSmallInteger('block_unit_type_id')->nullable(false)->change();
            
            // Re-add original foreign key constraints
            $table->foreign('block_building_id')->references('id')->on('block_buildings')->onDelete('cascade');
            $table->foreign('block_unit_type_id')->references('id')->on('block_unit_types');
        });
    }
};
