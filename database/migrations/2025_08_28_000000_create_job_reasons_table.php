<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * CONSOLIDATED MIGRATION - Combines:
     * - 2025_08_28_000000_create_job_reasons_table.php (initial table creation)
     * - 2025_09_14_060733_add_unique_to_job_reasons_name.php
     */
    public function up(): void
    {
        Schema::create('job_reasons', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 50)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_reasons');
    }
};

