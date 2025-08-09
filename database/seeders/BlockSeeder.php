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

        $blocks = [];
        for ($i = 1; $i <= 5; $i++) {
            $blocks[] = [
                'name' => 'Block ' . $i,
                'management_company' => 'Company ' . $i,
                'block_type_id' => $blockTypeId,
                'user_id' => $userId,
                'address1' => 'Address Line 1 - ' . $i,
                'address2' => null,
                'address3' => null,
                'image_path' => null,
                'image_name' => null,
                'country_id' => 1,
                'state_id' => 1,
                'car_spaces' => 10,
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
