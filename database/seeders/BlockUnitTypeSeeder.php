<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockUnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blockUnitTypes = [
            [
                'id' => 1,
                'name' => 'Apartment',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 2,
                'name' => 'Duplex',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 3,
                'name' => 'House',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 4,
                'name' => 'Commercial',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 5,
                'name' => 'Parking Space',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
        ];

        foreach ($blockUnitTypes as $blockUnitType) {
            DB::table('block_unit_types')->insertOrIgnore($blockUnitType);
        }
    }
}
