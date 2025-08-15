<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlockBuildingTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('block_building_types')->truncate();
        DB::table('block_building_types')->insert([
            [
                'id' => 1,
                'name' => 'Multi Level Apartment',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
            [
                'id' => 2,
                'name' => 'Duplex',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
            [
                'id' => 3,
                'name' => 'Houses',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
            [
                'id' => 4,
                'name' => 'Commercial Business Park',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
        ]);
    }
}
