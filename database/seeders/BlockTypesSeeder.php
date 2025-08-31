<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blockTypes = [
            ['name' => 'Residential Apartment'],
            ['name' => 'Commercial Building'],
            ['name' => 'Mixed Use Development'],
            ['name' => 'Office Building'],
            ['name' => 'Retail Complex'],
            ['name' => 'Industrial Building'],
            ['name' => 'Warehouse'],
            ['name' => 'Hotel'],
            ['name' => 'Resort'],
            ['name' => 'Educational Institution'],
            ['name' => 'Healthcare Facility'],
            ['name' => 'Government Building'],
            ['name' => 'Religious Building'],
            ['name' => 'Sports Complex'],
            ['name' => 'Parking Structure'],
        ];

        foreach ($blockTypes as $blockType) {
            DB::table('block_types')->insert([
                'name' => $blockType['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
