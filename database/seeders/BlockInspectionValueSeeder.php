<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockInspectionValue;

class BlockInspectionValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $values = [
            // Condition values
            ['block_inspection_value_type_id' => 1, 'name' => 'Excellent', 'description' => 'In excellent condition'],
            ['block_inspection_value_type_id' => 1, 'name' => 'Good', 'description' => 'In good condition'],
            ['block_inspection_value_type_id' => 1, 'name' => 'Fair', 'description' => 'In fair condition'],
            ['block_inspection_value_type_id' => 1, 'name' => 'Poor', 'description' => 'In poor condition'],
            ['block_inspection_value_type_id' => 1, 'name' => 'Critical', 'description' => 'In critical condition'],
            
            // Status values
            ['block_inspection_value_type_id' => 2, 'name' => 'Operational', 'description' => 'Fully operational'],
            ['block_inspection_value_type_id' => 2, 'name' => 'Partially Operational', 'description' => 'Partially operational'],
            ['block_inspection_value_type_id' => 2, 'name' => 'Non-Operational', 'description' => 'Not operational'],
            ['block_inspection_value_type_id' => 2, 'name' => 'Under Maintenance', 'description' => 'Currently under maintenance'],
            ['block_inspection_value_type_id' => 2, 'name' => 'Out of Service', 'description' => 'Out of service'],
            
            // Priority values
            ['block_inspection_value_type_id' => 3, 'name' => 'Low', 'description' => 'Low priority'],
            ['block_inspection_value_type_id' => 3, 'name' => 'Medium', 'description' => 'Medium priority'],
            ['block_inspection_value_type_id' => 3, 'name' => 'High', 'description' => 'High priority'],
            ['block_inspection_value_type_id' => 3, 'name' => 'Urgent', 'description' => 'Urgent priority'],
            ['block_inspection_value_type_id' => 3, 'name' => 'Critical', 'description' => 'Critical priority'],
            
            // Compliance values
            ['block_inspection_value_type_id' => 4, 'name' => 'Compliant', 'description' => 'Fully compliant'],
            ['block_inspection_value_type_id' => 4, 'name' => 'Partially Compliant', 'description' => 'Partially compliant'],
            ['block_inspection_value_type_id' => 4, 'name' => 'Non-Compliant', 'description' => 'Not compliant'],
            ['block_inspection_value_type_id' => 4, 'name' => 'Under Review', 'description' => 'Under review'],
            ['block_inspection_value_type_id' => 4, 'name' => 'Pending', 'description' => 'Pending compliance'],
            
            // Safety values
            ['block_inspection_value_type_id' => 5, 'name' => 'Safe', 'description' => 'Safe condition'],
            ['block_inspection_value_type_id' => 5, 'name' => 'Minor Issues', 'description' => 'Minor safety issues'],
            ['block_inspection_value_type_id' => 5, 'name' => 'Moderate Risk', 'description' => 'Moderate safety risk'],
            ['block_inspection_value_type_id' => 5, 'name' => 'High Risk', 'description' => 'High safety risk'],
            ['block_inspection_value_type_id' => 5, 'name' => 'Dangerous', 'description' => 'Dangerous condition'],
        ];

        foreach ($values as $value) {
            BlockInspectionValue::updateOrCreate(
                [
                    'block_inspection_value_type_id' => $value['block_inspection_value_type_id'],
                    'name' => $value['name']
                ],
                $value
            );
        }
    }
}
