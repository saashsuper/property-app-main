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
     * - 2025_08_28_000001_create_job_statuses_table.php (initial table creation)
     * - 2025_09_19_120834_fix_job_statuses_duplicates.php (adds unique constraint)
     */
    public function up(): void
    {
        Schema::create('job_statuses', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 50)->unique();
            $table->unsignedTinyInteger('is_updated')->default(1)->comment('Update throw edit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_statuses');
    }
};

