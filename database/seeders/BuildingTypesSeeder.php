<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $buildingTypes = [
            ['name' => 'High Rise'],
            ['name' => 'Mid Rise'],
            ['name' => 'Low Rise'],
            ['name' => 'Townhouse'],
            ['name' => 'Villa'],
            ['name' => 'Duplex'],
            ['name' => 'Triplex'],
            ['name' => 'Penthouse'],
            ['name' => 'Studio'],
            ['name' => 'Loft'],
            ['name' => 'Garden Apartment'],
            ['name' => 'Walk-up'],
            ['name' => 'Elevator Building'],
            ['name' => 'Co-op'],
            ['name' => 'Condominium'],
            ['name' => 'Rental Building'],
            ['name' => 'Mixed Use'],
            ['name' => 'Office Tower'],
            ['name' => 'Shopping Center'],
            ['name' => 'Industrial Complex'],
        ];

        foreach ($buildingTypes as $buildingType) {
            DB::table('building_types')->insert([
                'name' => $buildingType['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
