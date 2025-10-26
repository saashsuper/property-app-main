<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockInspectionValue;

class BlockInspectionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Data migrated from saashmagna.sql to match production database structure
     */
    public function run(): void
    {
        $values = [
            // Type 1: Yes/No
            ['id' => 1, 'block_inspection_value_type_id' => 1, 'value' => 'Yes', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 2, 'block_inspection_value_type_id' => 1, 'value' => 'No', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 3, 'block_inspection_value_type_id' => 1, 'value' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            
            // Type 2: Clean/Bad
            ['id' => 4, 'block_inspection_value_type_id' => 2, 'value' => 'Clean', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 5, 'block_inspection_value_type_id' => 2, 'value' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            ['id' => 6, 'block_inspection_value_type_id' => 2, 'value' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            
            // Type 3: Good/Avg/Poor
            ['id' => 7, 'block_inspection_value_type_id' => 3, 'value' => 'Good', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 8, 'block_inspection_value_type_id' => 3, 'value' => 'Average', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            ['id' => 9, 'block_inspection_value_type_id' => 3, 'value' => 'Poor', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            
            // Type 4: Working/Not Working/Not applicable
            ['id' => 10, 'block_inspection_value_type_id' => 4, 'value' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 11, 'block_inspection_value_type_id' => 4, 'value' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 12, 'block_inspection_value_type_id' => 4, 'value' => 'N/A', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            
            // Type 5: Working/Not Working/Not checked
            ['id' => 13, 'block_inspection_value_type_id' => 5, 'value' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 14, 'block_inspection_value_type_id' => 5, 'value' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 15, 'block_inspection_value_type_id' => 5, 'value' => 'Not checked', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            
            // Type 6: Working/Partially Working/No lights
            ['id' => 16, 'block_inspection_value_type_id' => 6, 'value' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 17, 'block_inspection_value_type_id' => 6, 'value' => 'Partially Working', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            ['id' => 18, 'block_inspection_value_type_id' => 6, 'value' => 'No lights', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            
            // Type 7: Working/Not Working/Needs Attention
            ['id' => 19, 'block_inspection_value_type_id' => 7, 'value' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 20, 'block_inspection_value_type_id' => 7, 'value' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 21, 'block_inspection_value_type_id' => 7, 'value' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            
            // Type 8: No faults/Faults/Needs Attention
            ['id' => 22, 'block_inspection_value_type_id' => 8, 'value' => 'No faults', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 23, 'block_inspection_value_type_id' => 8, 'value' => 'Faults', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 24, 'block_inspection_value_type_id' => 8, 'value' => 'Needs Attention', 'color' => 'btn-outline-primary', 'bg_color' => 'oranges'],
            
            // Type 9: Working/Not Working/No lights
            ['id' => 25, 'block_inspection_value_type_id' => 9, 'value' => 'Working', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
            ['id' => 26, 'block_inspection_value_type_id' => 9, 'value' => 'Not Working', 'color' => 'btn-outline-danger', 'bg_color' => 'reds'],
            ['id' => 27, 'block_inspection_value_type_id' => 9, 'value' => 'No lights', 'color' => 'btn-outline-success', 'bg_color' => 'greens'],
        ];

        foreach ($values as $value) {
            BlockInspectionValue::updateOrCreate(
                ['id' => $value['id']],
                $value
            );
        }
    }
}
