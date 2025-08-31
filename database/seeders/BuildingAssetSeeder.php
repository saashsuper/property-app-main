<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BuildingAsset;

class BuildingAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            // Structural Components (Condition type)
            ['id' => 1, 'name' => 'Foundation', 'block_inspection_value_type_id' => 1],
            ['id' => 2, 'name' => 'Walls', 'block_inspection_value_type_id' => 1],
            ['id' => 3, 'name' => 'Roof', 'block_inspection_value_type_id' => 1],
            ['id' => 4, 'name' => 'Ceiling', 'block_inspection_value_type_id' => 1],
            ['id' => 5, 'name' => 'Floor', 'block_inspection_value_type_id' => 1],
            ['id' => 6, 'name' => 'Windows', 'block_inspection_value_type_id' => 1],
            ['id' => 7, 'name' => 'Doors', 'block_inspection_value_type_id' => 1],
            ['id' => 8, 'name' => 'Stairs', 'block_inspection_value_type_id' => 1],
            ['id' => 9, 'name' => 'Elevators', 'block_inspection_value_type_id' => 1],
            ['id' => 10, 'name' => 'Balconies', 'block_inspection_value_type_id' => 1],
            
            // Electrical Systems (Status type)
            ['id' => 11, 'name' => 'Main Electrical Panel', 'block_inspection_value_type_id' => 2],
            ['id' => 12, 'name' => 'Electrical Wiring', 'block_inspection_value_type_id' => 2],
            ['id' => 13, 'name' => 'Lighting Systems', 'block_inspection_value_type_id' => 2],
            ['id' => 14, 'name' => 'Emergency Lighting', 'block_inspection_value_type_id' => 2],
            ['id' => 15, 'name' => 'Power Outlets', 'block_inspection_value_type_id' => 2],
            ['id' => 16, 'name' => 'Circuit Breakers', 'block_inspection_value_type_id' => 2],
            ['id' => 17, 'name' => 'Fire Alarm System', 'block_inspection_value_type_id' => 2],
            ['id' => 18, 'name' => 'Security System', 'block_inspection_value_type_id' => 2],
            
            // Plumbing Systems (Status type)
            ['id' => 19, 'name' => 'Water Supply', 'block_inspection_value_type_id' => 2],
            ['id' => 20, 'name' => 'Drainage System', 'block_inspection_value_type_id' => 2],
            ['id' => 21, 'name' => 'Water Heaters', 'block_inspection_value_type_id' => 2],
            ['id' => 22, 'name' => 'Pipes', 'block_inspection_value_type_id' => 2],
            ['id' => 23, 'name' => 'Faucets', 'block_inspection_value_type_id' => 2],
            ['id' => 24, 'name' => 'Toilets', 'block_inspection_value_type_id' => 2],
            ['id' => 25, 'name' => 'Sinks', 'block_inspection_value_type_id' => 2],
            ['id' => 26, 'name' => 'Showers', 'block_inspection_value_type_id' => 2],
            
            // HVAC Systems (Status type)
            ['id' => 27, 'name' => 'Air Conditioning', 'block_inspection_value_type_id' => 2],
            ['id' => 28, 'name' => 'Heating System', 'block_inspection_value_type_id' => 2],
            ['id' => 29, 'name' => 'Ventilation', 'block_inspection_value_type_id' => 2],
            ['id' => 30, 'name' => 'Air Filters', 'block_inspection_value_type_id' => 2],
            ['id' => 31, 'name' => 'Ductwork', 'block_inspection_value_type_id' => 2],
            
            // Safety Equipment (Safety type)
            ['id' => 32, 'name' => 'Fire Extinguishers', 'block_inspection_value_type_id' => 5],
            ['id' => 33, 'name' => 'Smoke Detectors', 'block_inspection_value_type_id' => 5],
            ['id' => 34, 'name' => 'Carbon Monoxide Detectors', 'block_inspection_value_type_id' => 5],
            ['id' => 35, 'name' => 'Emergency Exits', 'block_inspection_value_type_id' => 5],
            ['id' => 36, 'name' => 'Fire Sprinklers', 'block_inspection_value_type_id' => 5],
            ['id' => 37, 'name' => 'Handrails', 'block_inspection_value_type_id' => 5],
            ['id' => 38, 'name' => 'Safety Signs', 'block_inspection_value_type_id' => 5],
            
            // Common Areas (Condition type)
            ['id' => 39, 'name' => 'Lobby', 'block_inspection_value_type_id' => 1],
            ['id' => 40, 'name' => 'Corridors', 'block_inspection_value_type_id' => 1],
            ['id' => 41, 'name' => 'Parking Area', 'block_inspection_value_type_id' => 1],
            ['id' => 42, 'name' => 'Garden/Landscaping', 'block_inspection_value_type_id' => 1],
            ['id' => 43, 'name' => 'Pool', 'block_inspection_value_type_id' => 1],
            ['id' => 44, 'name' => 'Gym', 'block_inspection_value_type_id' => 1],
            ['id' => 45, 'name' => 'Playground', 'block_inspection_value_type_id' => 1],
            
            // Compliance Items (Compliance type)
            ['id' => 46, 'name' => 'Building Permits', 'block_inspection_value_type_id' => 4],
            ['id' => 47, 'name' => 'Safety Certificates', 'block_inspection_value_type_id' => 4],
            ['id' => 48, 'name' => 'Insurance Documents', 'block_inspection_value_type_id' => 4],
            ['id' => 49, 'name' => 'Maintenance Records', 'block_inspection_value_type_id' => 4],
            ['id' => 50, 'name' => 'Inspection Reports', 'block_inspection_value_type_id' => 4],
        ];

        foreach ($assets as $asset) {
            BuildingAsset::updateOrCreate(
                ['id' => $asset['id']],
                $asset
            );
        }
    }
}
