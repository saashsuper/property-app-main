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
        // Simply modify the columns to be nullable using raw SQL
        DB::statement('ALTER TABLE block_units MODIFY block_building_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE block_units MODIFY block_unit_type_id SMALLINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change columns back to non-nullable
        DB::statement('ALTER TABLE block_units MODIFY block_building_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE block_units MODIFY block_unit_type_id SMALLINT UNSIGNED NOT NULL');
    }
};
