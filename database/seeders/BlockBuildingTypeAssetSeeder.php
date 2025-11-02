<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockBuildingTypeAsset;
use Illuminate\Support\Facades\DB;

class BlockBuildingTypeAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds block_building_type_assets with EXACT data from building_type_assets table in saashmagna.sql
     * 
     * Original SQL data (11 records):
     * (1, 0, 1), (2, 0, 2), (3, 0, 3), (4, 0, 4),  -- Type 0: General assets (1-4)
     * (5, 1, 5), (6, 1, 6), (7, 1, 7), (8, 1, 8), (9, 1, 9), (10, 1, 10),  -- Type 1: Assets 5-10
     * (11, 2, 5)  -- Type 2: Asset 5
     * 
     * Note: building_type_id = 0 in original means these are general assets applicable to all building types
     */
    public function run(): void
    {
        // Disable foreign key checks to allow building_type_id = 0
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear existing data
        DB::table('block_building_type_assets')->truncate();

        // EXACT data from saashmagna.sql building_type_assets table (11 records)
        // Mapping: building_type_id → block_building_type_id, building_asset_id → block_building_asset_id
        $data = [
            // Type 0: General assets (applicable to all building types)
            ['id' => 1, 'block_building_type_id' => 0, 'block_building_asset_id' => 1, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 2, 'block_building_type_id' => 0, 'block_building_asset_id' => 2, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 3, 'block_building_type_id' => 0, 'block_building_asset_id' => 3, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 4, 'block_building_type_id' => 0, 'block_building_asset_id' => 4, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 1: Building-specific assets
            ['id' => 5, 'block_building_type_id' => 1, 'block_building_asset_id' => 5, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 6, 'block_building_type_id' => 1, 'block_building_asset_id' => 6, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 7, 'block_building_type_id' => 1, 'block_building_asset_id' => 7, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 8, 'block_building_type_id' => 1, 'block_building_asset_id' => 8, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 9, 'block_building_type_id' => 1, 'block_building_asset_id' => 9, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            ['id' => 10, 'block_building_type_id' => 1, 'block_building_asset_id' => 10, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
            
            // Type 2: Building-specific asset
            ['id' => 11, 'block_building_type_id' => 2, 'block_building_asset_id' => 5, 'created_at' => '2022-09-18 08:24:12', 'updated_at' => '2022-09-18 08:24:12'],
        ];

        // Insert all records
        DB::table('block_building_type_assets')->insert($data);
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('✅ Successfully seeded ' . count($data) . ' records into block_building_type_assets table');
        $this->command->info('   - Type 0 (General): 4 assets (1-4)');
        $this->command->info('   - Type 1: 6 assets (5-10)');
        $this->command->info('   - Type 2: 1 asset (5)');
        $this->command->info('   📝 Note: Type 0 means general assets applicable to all building types');
    }
}
