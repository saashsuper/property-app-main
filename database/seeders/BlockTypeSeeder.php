<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blockTypes = [
            [
                'id' => 1,
                'name' => 'Residential',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 2,
                'name' => 'Commercial',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
            [
                'id' => 3,
                'name' => 'Residential + Commercial',
                'created_at' => '2022-09-18 08:24:12',
                'updated_at' => '2022-09-18 08:24:12',
            ],
        ];

        foreach ($blockTypes as $blockType) {
            DB::table('block_types')->insertOrIgnore($blockType);
        }
    }
}
