<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlockUnitTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Use delete instead of truncate to avoid FK constraint errors
        \DB::table('block_unit_types')->delete();
        \DB::table('block_unit_types')->insert([
            [
                'id' => 1,
                'name' => 'Apartment',
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
                'name' => 'House',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
            [
                'id' => 4,
                'name' => 'Commercial',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
            [
                'id' => 5,
                'name' => 'Parking Space',
                'created_at' => Carbon::parse('2022-09-18 08:24:12'),
                'updated_at' => Carbon::parse('2022-09-18 08:24:12'),
            ],
        ]);
    }
}
