<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add foreign key constraint for block_general_asset_id
     * This runs after block_general_assets table is created
     */
    public function up(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            $table->foreign('block_general_asset_id')
                  ->references('id')
                  ->on('block_general_assets')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            $table->dropForeign(['block_general_asset_id']);
        });
    }
};

