<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BlockInspectionValue;

class BlockInspectionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data migrated from saashmagna.sql to match production database structure
     * 
     * This seeder truncates the table and inserts 27 fresh records
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate the table to ensure clean data
        DB::table('block_inspection_values')->truncate();
        
        $values = [
            // Type 1: Yes/No
            ['id' => 1, 'block_inspection_value_type_id' => 1, 'name' => 'Yes', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 2, 'block_inspection_value_type_id' => 1, 'name' => 'No', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 3, 'block_inspection_value_type_id' => 1, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 2: Clean/Bad
            ['id' => 4, 'block_inspection_value_type_id' => 2, 'name' => 'Clean', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 5, 'block_inspection_value_type_id' => 2, 'name' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 6, 'block_inspection_value_type_id' => 2, 'name' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 3: Good/Avg/Poor
            ['id' => 7, 'block_inspection_value_type_id' => 3, 'name' => 'Good', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 8, 'block_inspection_value_type_id' => 3, 'name' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 9, 'block_inspection_value_type_id' => 3, 'name' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 4: Working/Not Working/Not applicable
            ['id' => 10, 'block_inspection_value_type_id' => 4, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 11, 'block_inspection_value_type_id' => 4, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 12, 'block_inspection_value_type_id' => 4, 'name' => 'N/A', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 5: Working/Not Working/Not checked
            ['id' => 13, 'block_inspection_value_type_id' => 5, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 14, 'block_inspection_value_type_id' => 5, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 15, 'block_inspection_value_type_id' => 5, 'name' => 'Not checked', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 6: Working/Partially Working/No lights
            ['id' => 16, 'block_inspection_value_type_id' => 6, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 17, 'block_inspection_value_type_id' => 6, 'name' => 'Partially Working', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 18, 'block_inspection_value_type_id' => 6, 'name' => 'No lights', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 7: Working/Not Working/Needs Attention
            ['id' => 19, 'block_inspection_value_type_id' => 7, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 20, 'block_inspection_value_type_id' => 7, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 21, 'block_inspection_value_type_id' => 7, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 8: No faults/Faults/Needs Attention
            ['id' => 22, 'block_inspection_value_type_id' => 8, 'name' => 'No faults', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 23, 'block_inspection_value_type_id' => 8, 'name' => 'Faults', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 24, 'block_inspection_value_type_id' => 8, 'name' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 9: Working/Not Working/No lights
            ['id' => 25, 'block_inspection_value_type_id' => 9, 'name' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 26, 'block_inspection_value_type_id' => 9, 'name' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 27, 'block_inspection_value_type_id' => 9, 'name' => 'No lights', 'color' => 'btn-outline-success', 'bg_color' => 'greens', 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
        ];

        // Insert all records
        DB::table('block_inspection_values')->insert($values);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Successfully seeded 27 records into block_inspection_values table');
    }
}
