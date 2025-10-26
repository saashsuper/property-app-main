<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockBuildingAsset;
use Illuminate\Support\Facades\DB;

class BlockBuildingAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds the 10 original building assets from saashmagna.sql
     * Using exact block_inspection_value_type_id values from the original building_assets table
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('block_building_assets')->truncate();

        // Original 10 assets from saashmagna.sql building_assets table
        // Using exact block_inspection_value_type_id values from production database
        $assets = [
            ['id' => 1, 'name' => 'Gates', 'block_inspection_value_type_id' => 4],
            ['id' => 2, 'name' => 'Landscape', 'block_inspection_value_type_id' => 2],
            ['id' => 3, 'name' => 'Street Lights', 'block_inspection_value_type_id' => 5],
            ['id' => 4, 'name' => 'Building Externals', 'block_inspection_value_type_id' => 3],
            ['id' => 5, 'name' => 'Stairs', 'block_inspection_value_type_id' => 2],
            ['id' => 6, 'name' => 'Lights', 'block_inspection_value_type_id' => 4],
            ['id' => 7, 'name' => 'Lifts', 'block_inspection_value_type_id' => 7],
            ['id' => 8, 'name' => 'Walls', 'block_inspection_value_type_id' => 3],
            ['id' => 9, 'name' => 'Fire Alarm', 'block_inspection_value_type_id' => 8],
            ['id' => 10, 'name' => 'Doors/Fire Doors', 'block_inspection_value_type_id' => 4],
        ];

        foreach ($assets as $asset) {
            BlockBuildingAsset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }

        $this->command->info('Successfully seeded ' . count($assets) . ' block building asset records.');
    }
}
