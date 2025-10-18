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
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Make comments column nullable
            $table->string('comments', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('block_inspection_assets', function (Blueprint $table) {
            // Revert to non-nullable
            $table->string('comments', 255)->nullable(false)->change();
        });
    }
};

