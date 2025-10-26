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
     * This migration truncates the block_inspection_values table and 
     * inserts the 27 production records from saashmagna.sql
     */
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        DB::table('block_inspection_values')->truncate();
        
        // Insert all 27 records from production
        DB::table('block_inspection_values')->insert([
            ['id' => 1, 'block_inspection_value_type_id' => 1, 'name' => 'Yes', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 2, 'block_inspection_value_type_id' => 1, 'name' => 'No', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 3, 'block_inspection_value_type_id' => 1, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 4, 'block_inspection_value_type_id' => 2, 'name' => 'Clean', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 5, 'block_inspection_value_type_id' => 2, 'name' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 6, 'block_inspection_value_type_id' => 2, 'name' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 7, 'block_inspection_value_type_id' => 3, 'name' => 'Good', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 8, 'block_inspection_value_type_id' => 3, 'name' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 9, 'block_inspection_value_type_id' => 3, 'name' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 10, 'block_inspection_value_type_id' => 4, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 11, 'block_inspection_value_type_id' => 4, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 12, 'block_inspection_value_type_id' => 4, 'name' => 'N/A', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 13, 'block_inspection_value_type_id' => 5, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 14, 'block_inspection_value_type_id' => 5, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 15, 'block_inspection_value_type_id' => 5, 'name' => 'Not checked', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 16, 'block_inspection_value_type_id' => 6, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 17, 'block_inspection_value_type_id' => 6, 'name' => 'Partially Working', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 18, 'block_inspection_value_type_id' => 6, 'name' => 'No lights', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 19, 'block_inspection_value_type_id' => 7, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 20, 'block_inspection_value_type_id' => 7, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 21, 'block_inspection_value_type_id' => 7, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 22, 'block_inspection_value_type_id' => 8, 'name' => 'No faults', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 23, 'block_inspection_value_type_id' => 8, 'name' => 'Faults', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 24, 'block_inspection_value_type_id' => 8, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 25, 'block_inspection_value_type_id' => 9, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 26, 'block_inspection_value_type_id' => 9, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 27, 'block_inspection_value_type_id' => 9, 'name' => 'No lights', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
        ]);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table
        DB::table('block_inspection_values')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};

