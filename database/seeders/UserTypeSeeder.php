<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userTypes = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'description' => 'Super Administrator with full system access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'description' => 'Administrator with management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Financial Admin',
                'description' => 'Financial Administrator with financial management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Property manager',
                'description' => 'Property Manager with property management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'name' => 'Office Administrator',
                'description' => 'Office Administrator with administrative access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Assistant Property Manager',
                'description' => 'Assistant Property Manager with limited management access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($userTypes as $userType) {
            DB::table('user_types')->insertOrIgnore($userType);
        }
    }
}
