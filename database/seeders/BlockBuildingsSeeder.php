<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockBuildingsSeeder extends Seeder
{
    public function run(): void
    {
        $blockIds = DB::table('blocks')->pluck('id');
        $buildingTypeId = DB::table('building_types')->value('id');

        if ($blockIds->isEmpty() || !$buildingTypeId) {
            $this->command->warn('Skipping BlockBuildingsSeeder: missing blocks or building_types');
            return;
        }

        $rows = [];
        foreach ($blockIds as $blockId) {
            for ($i = 1; $i <= 2; $i++) {
                $rows[] = [
                    'block_id' => $blockId,
                    'name' => 'Building ' . $i,
                    'building_type_id' => $buildingTypeId,
                    'floor_no' => 5,
                    'roof_type' => 'Flat',
                    'no_lift' => 1,
                    'image_path' => null,
                    'image_name' => null,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('block_buildings')->insertOrIgnore($rows);
    }
}
