<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildingTypes = [
            [
                'id' => 1,
                'name' => 'Multi Level Apartment',
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
                'name' => 'Houses',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 4,
                'name' => 'Commercial Business Park',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
        ];

        foreach ($buildingTypes as $buildingType) {
            DB::table('building_types')->insertOrIgnore($buildingType);
        }
    }
}
