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
            ['id' => 1, 'name' => 'Condition'],
            ['id' => 2, 'name' => 'Status'],
            ['id' => 3, 'name' => 'Priority'],
            ['id' => 4, 'name' => 'Compliance'],
            ['id' => 5, 'name' => 'Safety'],
        ];

        foreach ($valueTypes as $valueType) {
            BlockInspectionValueType::updateOrCreate(
                ['id' => $valueType['id']],
                $valueType
            );
        }
    }
}
