<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlockSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure dependencies exist: users, block_types
        $userId = DB::table('users')->value('id');
        $blockTypeId = DB::table('block_types')->value('id');

        if (!$userId || !$blockTypeId) {
            $this->command->warn('Skipping BlockSeeder: missing users or block_types');
            return;
        }

        $blockNames = [
            ['name' => 'Greenwood Residences', 'company' => 'Apex Property Group'],
            ['name' => 'Maple Heights', 'company' => 'BlueSky Estates'],
            ['name' => 'Lakeside Towers', 'company' => 'Urban Living Co.'],
            ['name' => 'Sunset Villas', 'company' => 'Riverstone Realty'],
            ['name' => 'Cedar Park Apartments', 'company' => 'Evergreen Properties'],
            ['name' => 'Oakwood Estate', 'company' => 'PrimeHome Management'],
        ];

        $blocks = [];
        foreach ($blockNames as $idx => $meta) {
            $blocks[] = [
                'name' => $meta['name'],
                'management_company' => $meta['company'],
                'block_type_id' => $blockTypeId,
                'user_id' => $userId,
                'address1' => ($idx + 10) . ' Main Street',
                'address2' => null,
                'address3' => null,
                'image_path' => null,
                'image_name' => null,
                'country_id' => 1,
                'state_id' => 1,
                'car_spaces' => 10 + $idx,
                'inspection_count' => 0,
                'no_of_units' => 0,
                'created_by' => $userId,
                'updated_by' => $userId,
                'deleted_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('blocks')->insertOrIgnore($blocks);
    }
}
