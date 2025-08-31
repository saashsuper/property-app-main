<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockBuildingsSeeder extends Seeder
{
    public function run(): void
    {
        // Check if we have blocks and building types
        $blockIds = DB::table('blocks')->pluck('id');
        $buildingTypeIds = DB::table('block_building_types')->pluck('id');

        if ($blockIds->isEmpty() || $buildingTypeIds->isEmpty()) {
            $this->command->warn('Skipping BlockBuildingsSeeder: missing blocks or block_building_types');
            return;
        }

        // Real building data from the SQL file (adjusted for existing blocks)
        $buildings = [
            // Block 1 buildings (Fairgreen)
            [
                'block_id' => 1,
                'name' => 'Tower 1',
                'building_type_id' => 1,
                'floor_no' => 4,
                'roof_type' => 'Flat',
                'no_lift' => 1,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:45:20',
                'updated_at' => '2022-12-31 14:45:20',
            ],
            [
                'block_id' => 1,
                'name' => 'Tower 2',
                'building_type_id' => 1,
                'floor_no' => 4,
                'roof_type' => 'Flat',
                'no_lift' => 1,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:45:34',
                'updated_at' => '2022-12-31 14:45:34',
            ],
            [
                'block_id' => 1,
                'name' => 'Tower 3',
                'building_type_id' => 1,
                'floor_no' => 4,
                'roof_type' => 'Flat',
                'no_lift' => 1,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:45:52',
                'updated_at' => '2022-12-31 14:45:52',
            ],
            [
                'block_id' => 1,
                'name' => 'Piazza View',
                'building_type_id' => 1,
                'floor_no' => 2,
                'roof_type' => 'Flat',
                'no_lift' => 0,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:46:17',
                'updated_at' => '2022-12-31 14:46:17',
            ],
            [
                'block_id' => 1,
                'name' => 'Westside 1-4',
                'building_type_id' => 2,
                'floor_no' => 2,
                'roof_type' => 'Tiled Roof',
                'no_lift' => 0,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:46:52',
                'updated_at' => '2022-12-31 14:46:52',
            ],
            [
                'block_id' => 1,
                'name' => 'Westside 5-8',
                'building_type_id' => 2,
                'floor_no' => 2,
                'roof_type' => 'Tiled Roof',
                'no_lift' => 0,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:47:22',
                'updated_at' => '2022-12-31 14:47:22',
            ],
            [
                'block_id' => 1,
                'name' => 'Westside 9-12',
                'building_type_id' => 2,
                'floor_no' => 2,
                'roof_type' => 'Tiled Roof',
                'no_lift' => 0,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2022-12-31 14:47:43',
                'updated_at' => '2022-12-31 14:47:43',
            ],
            // Block 2 buildings (2 George's Quay)
            [
                'block_id' => 2,
                'name' => '2 Georges Quay',
                'building_type_id' => 1,
                'floor_no' => 4,
                'roof_type' => 'Flat Roof',
                'no_lift' => 1,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2023-03-24 11:25:55',
                'updated_at' => '2023-03-24 11:25:55',
            ],
            // Block 3 buildings (Airways Business Park) - using block 3 instead of 22
            [
                'block_id' => 3,
                'name' => 'The Mews 1-12',
                'building_type_id' => 1,
                'floor_no' => 4,
                'roof_type' => 'Pitch Roof',
                'no_lift' => 1,
                'image_path' => null,
                'image_name' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2025-08-11 07:06:45',
                'updated_at' => '2025-08-11 07:06:45',
            ],
        ];

        // Filter buildings to only include those with valid block_ids and building_type_ids
        $validBuildings = array_filter($buildings, function($building) use ($blockIds, $buildingTypeIds) {
            return $blockIds->contains($building['block_id']) && 
                   $buildingTypeIds->contains($building['building_type_id']);
        });

        if (empty($validBuildings)) {
            $this->command->warn('No valid buildings to seed (block_id or building_type_id not found)');
            return;
        }

        // Insert the buildings
        foreach ($validBuildings as $building) {
            DB::table('block_buildings')->insertOrIgnore($building);
        }

        $this->command->info('BlockBuildingsSeeder completed successfully. Created ' . count($validBuildings) . ' buildings.');
    }
}
