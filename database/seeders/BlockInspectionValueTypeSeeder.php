<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockInspectionValueType;

class BlockInspectionValueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $valueTypes = [
            ['id' => 1, 'name' => 'Yes/No'],
            ['id' => 2, 'name' => 'Clean/Bad'],
            ['id' => 3, 'name' => 'Good/Avg/Poor'],
            ['id' => 4, 'name' => 'Working/Not Working/Not applicable'],
            ['id' => 5, 'name' => 'Working/Not Working/Not checked '],
            ['id' => 6, 'name' => 'Working/Partially Working/No lights'],
            ['id' => 7, 'name' => 'Working/Not Working/Needs Attention'],
            ['id' => 8, 'name' => 'No faults/Faults/Needs Attention'],
            ['id' => 9, 'name' => 'Working/Not Working/No lights'],
        ];

        foreach ($valueTypes as $valueType) {
            BlockInspectionValueType::updateOrCreate(
                ['id' => $valueType['id']],
                $valueType
            );
        }
    }
}
