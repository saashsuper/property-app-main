<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration copies data from the 'value' field to the 'name' field
     * based on the production data from saashmagna.sql file.
     */
    public function up(): void
    {
        // Data from saashmagna.sql - mapping id to value field content
        $valuesToUpdate = [
            1 => 'Yes',
            2 => 'No',
            3 => 'Needs Attention',
            4 => 'Clean',
            5 => 'Average',
            6 => 'Poor',
            7 => 'Good',
            8 => 'Average',
            9 => 'Poor',
            10 => 'Working',
            11 => 'Not Working',
            12 => 'N/A',
            13 => 'Working',
            14 => 'Not Working',
            15 => 'Not checked',
            16 => 'Working',
            17 => 'Partially Working',
            18 => 'No lights',
            19 => 'Working',
            20 => 'Not Working',
            21 => 'Needs Attention',
            22 => 'No faults',
            23 => 'Faults',
            24 => 'Needs Attention',
            25 => 'Working',
            26 => 'Not Working',
            27 => 'No lights',
        ];

        // Update each record with the name field value
        foreach ($valuesToUpdate as $id => $name) {
            DB::table('block_inspection_values')
                ->where('id', $id)
                ->update([
                    'name' => $name,
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the name field values if rolling back
        DB::table('block_inspection_values')
            ->update([
                'name' => null,
                'updated_at' => now(),
            ]);
    }
};

