<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockUnitsSeeder extends Seeder
{
    public function run(): void
    {
        $blockIds = DB::table('blocks')->pluck('id');
        $buildingIds = DB::table('block_buildings')->pluck('id');
        $unitTypeId = DB::table('block_unit_types')->value('id');

        if ($blockIds->isEmpty() || $buildingIds->isEmpty() || !$unitTypeId) {
            $this->command->warn('Skipping BlockUnitsSeeder: missing blocks, block_buildings or block_unit_types');
            return;
        }

        $rows = [];
        foreach ($blockIds as $blockId) {
            foreach ($buildingIds as $buildingId) {
                for ($i = 1; $i <= 3; $i++) {
                    $rows[] = [
                        'block_id' => $blockId,
                        'block_building_id' => $buildingId,
                        'block_unit_type_id' => $unitTypeId,
                        'unit_code' => 'U' . $buildingId . '-' . $i,
                        'unit_name' => 'Unit ' . $i,
                        'owners_name' => 'Owner ' . $i,
                        'salutation' => 'Mr',
                        'email' => 'owner' . $buildingId . '_' . $i . '@example.com',
                        'resident' => true,
                        'address1' => 'Address 1',
                        'address2' => null,
                        'address3' => null,
                        'country_id' => 1,
                        'state_id' => 1,
                        'zip' => '00000',
                        'mobile_no' => null,
                        'phone_number' => null,
                        'letting_agent' => null,
                        'misc_info' => null,
                        'created_by' => 1,
                        'updated_by' => 1,
                        'deleted_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        DB::table('block_units')->insertOrIgnore($rows);
    }
}
