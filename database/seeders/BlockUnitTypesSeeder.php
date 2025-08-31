<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockUnitTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypes = [
            ['name' => 'Studio'],
            ['name' => '1 Bedroom'],
            ['name' => '2 Bedroom'],
            ['name' => '3 Bedroom'],
            ['name' => '4 Bedroom'],
            ['name' => '5+ Bedroom'],
            ['name' => 'Penthouse'],
            ['name' => 'Loft'],
            ['name' => 'Duplex'],
            ['name' => 'Triplex'],
            ['name' => 'Garden Unit'],
            ['name' => 'Basement Unit'],
            ['name' => 'Attic Unit'],
            ['name' => 'Corner Unit'],
            ['name' => 'End Unit'],
            ['name' => 'Middle Unit'],
            ['name' => 'Ground Floor'],
            ['name' => 'Upper Floor'],
            ['name' => 'Commercial Space'],
            ['name' => 'Office Space'],
            ['name' => 'Retail Space'],
            ['name' => 'Storage Unit'],
            ['name' => 'Parking Space'],
        ];

        foreach ($unitTypes as $unitType) {
            DB::table('block_unit_types')->insert([
                'name' => $unitType['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
