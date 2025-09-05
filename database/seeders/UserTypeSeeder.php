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
                'is_hidden' => true,
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
            [
                'id' => 7,
                'name' => 'Contractor Admin',
                'description' => 'Contractor administrator with management capabilities for contractor operations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'name' => 'Contractor User',
                'description' => 'Contractor user with limited access for contract-related tasks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($userTypes as $userType) {
            // Upsert to keep is_hidden in sync if record exists
            DB::table('user_types')->updateOrInsert(
                ['id' => $userType['id']],
                $userType
            );
        }
    }
}
