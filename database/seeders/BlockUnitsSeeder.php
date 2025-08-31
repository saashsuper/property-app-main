<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockUnitsSeeder extends Seeder
{
    public function run(): void
    {
        // Check if we have blocks, buildings, and unit types
        $blockIds = DB::table('blocks')->pluck('id');
        $buildingIds = DB::table('block_buildings')->pluck('id');
        $unitTypeIds = DB::table('block_unit_types')->pluck('id');

        if ($blockIds->isEmpty() || $buildingIds->isEmpty() || $unitTypeIds->isEmpty()) {
            $this->command->warn('Skipping BlockUnitsSeeder: missing blocks, buildings, or unit types');
            return;
        }

        // Real unit data from the SQL file
        $units = [
            [
                'block_id' => 2,
                'block_building_id' => 8,
                'block_unit_type_id' => 4,
                'unit_code' => 'GF',
                'unit_name' => 'Ground Floor',
                'owners_name' => 'CKT',
                'salutation' => 'Dear Sirs',
                'email' => 'info@ckt.ie',
                'resident' => 1,
                'address1' => null,
                'address2' => null,
                'address3' => null,
                'country_id' => 105,
                'state_id' => 0,
                'zip' => '',
                'mobile_no' => null,
                'phone_number' => null,
                'letting_agent' => null,
                'misc_info' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2023-03-24 11:26:44',
                'updated_at' => '2023-03-24 11:26:44',
            ],
            [
                'block_id' => 2,
                'block_building_id' => 8,
                'block_unit_type_id' => 4,
                'unit_code' => '1FGQ',
                'unit_name' => '1st Floor',
                'owners_name' => 'CKT',
                'salutation' => 'Dear Sirs',
                'email' => 'info@ckt.ie',
                'resident' => 1,
                'address1' => null,
                'address2' => null,
                'address3' => null,
                'country_id' => 105,
                'state_id' => 0,
                'zip' => '',
                'mobile_no' => null,
                'phone_number' => null,
                'letting_agent' => null,
                'misc_info' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2023-03-24 11:27:19',
                'updated_at' => '2023-03-24 11:27:19',
            ],
            [
                'block_id' => 2,
                'block_building_id' => 8,
                'block_unit_type_id' => 4,
                'unit_code' => '2FGQ',
                'unit_name' => '2nd Floor',
                'owners_name' => 'CKT',
                'salutation' => 'Dear Sirs',
                'email' => 'info@ckt.ie',
                'resident' => 1,
                'address1' => null,
                'address2' => null,
                'address3' => null,
                'country_id' => 105,
                'state_id' => 0,
                'zip' => '',
                'mobile_no' => null,
                'phone_number' => null,
                'letting_agent' => null,
                'misc_info' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2023-03-24 11:27:46',
                'updated_at' => '2023-03-24 11:27:46',
            ],
            [
                'block_id' => 2,
                'block_building_id' => 8,
                'block_unit_type_id' => 4,
                'unit_code' => '3FGQ',
                'unit_name' => 'Third Floor',
                'owners_name' => 'Cohesity International',
                'salutation' => 'Dear Sirs',
                'email' => 'info@cohesity.ie',
                'resident' => 1,
                'address1' => null,
                'address2' => null,
                'address3' => null,
                'country_id' => 105,
                'state_id' => 0,
                'zip' => '',
                'mobile_no' => null,
                'phone_number' => null,
                'letting_agent' => null,
                'misc_info' => null,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => null,
                'created_at' => '2023-03-24 11:28:23',
                'updated_at' => '2023-03-24 11:28:23',
            ],
        ];

        // Filter units to only include those with valid foreign keys
        $validUnits = array_filter($units, function($unit) use ($blockIds, $buildingIds, $unitTypeIds) {
            return $blockIds->contains($unit['block_id']) && 
                   $buildingIds->contains($unit['block_building_id']) && 
                   $unitTypeIds->contains($unit['block_unit_type_id']);
        });

        if (empty($validUnits)) {
            $this->command->warn('No valid units to seed (block_id, building_id, or unit_type_id not found)');
            return;
        }

        // Insert the units
        foreach ($validUnits as $unit) {
            DB::table('block_units')->insertOrIgnore($unit);
        }

        $this->command->info('BlockUnitsSeeder completed successfully. Created ' . count($validUnits) . ' units.');
    }
}
