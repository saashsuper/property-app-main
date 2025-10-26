<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename 'value' column back to 'name' in block_inspection_values table
     */
    public function up(): void
    {
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->renameColumn('value', 'name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_values', function (Blueprint $table) {
            $table->renameColumn('name', 'value');
        });
    }
};
