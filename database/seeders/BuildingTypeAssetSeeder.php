<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BuildingTypeAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::parse('2022-09-18 08:24:12');

        $buildingTypeAssets = [
            ['id' => 1, 'building_type_id' => 0, 'building_asset_id' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'building_type_id' => 0, 'building_asset_id' => 2, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'building_type_id' => 0, 'building_asset_id' => 3, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'building_type_id' => 0, 'building_asset_id' => 4, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'building_type_id' => 1, 'building_asset_id' => 5, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'building_type_id' => 1, 'building_asset_id' => 6, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'building_type_id' => 1, 'building_asset_id' => 7, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'building_type_id' => 1, 'building_asset_id' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'building_type_id' => 1, 'building_asset_id' => 9, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'building_type_id' => 1, 'building_asset_id' => 10, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'building_type_id' => 2, 'building_asset_id' => 5, 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('building_type_assets')->insert($buildingTypeAssets);
    }
}

