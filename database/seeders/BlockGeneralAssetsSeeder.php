<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockGeneralAsset;

class BlockGeneralAssetsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $generalAssets = [
            ['id' => 1, 'name' => 'Gates', 'block_inspection_value_type_id' => 1],
            ['id' => 2, 'name' => 'Landscape', 'block_inspection_value_type_id' => 1],
            ['id' => 3, 'name' => 'Street Lights', 'block_inspection_value_type_id' => 1],
            ['id' => 4, 'name' => 'Building Externals', 'block_inspection_value_type_id' => 1],
        ];

        foreach ($generalAssets as $asset) {
            BlockGeneralAsset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }
    }
}

