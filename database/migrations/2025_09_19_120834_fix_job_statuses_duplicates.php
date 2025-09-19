<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, remove all duplicate entries, keeping only the first occurrence of each name
        $uniqueStatuses = DB::table('job_statuses')
            ->select('name', DB::raw('MIN(id) as min_id'))
            ->groupBy('name')
            ->get();

        // Delete all records except the ones with minimum IDs
        foreach ($uniqueStatuses as $status) {
            DB::table('job_statuses')
                ->where('name', $status->name)
                ->where('id', '!=', $status->min_id)
                ->delete();
        }

        // Add unique constraint to prevent future duplicates
        Schema::table('job_statuses', function (Blueprint $table) {
            $table->unique('name', 'job_statuses_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the unique constraint
        Schema::table('job_statuses', function (Blueprint $table) {
            $table->dropUnique('job_statuses_name_unique');
        });
    }
};
