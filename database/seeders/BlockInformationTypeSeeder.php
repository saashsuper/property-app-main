<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockInformationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Truncate child and parent tables
        \DB::table('block_information')->truncate();
        \DB::table('block_information_types')->truncate();
        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $blockInformationTypes = [
            'Access Control/Zapper/Keys',
            'Air Conditioning',
            'CCTV',
            'Cleaning',
            'Drains',
            'Electrician',
            'ESB Meters',
            'Gas Meters',
            'Fire Alarm /Extng.',
            'General Maint.',
            'Grass Cutting/Landscaping',
            'Handyman/ Janitor',
            'Intercom System',
            'Lifts',
            'Pumps',
            'Waste Collection',
        ];

        foreach ($blockInformationTypes as $infoType) {
            DB::table('block_information_types')->insert([
                'name' => $infoType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
