<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RealUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add users using insertOrIgnore to avoid conflicts
        $users = [
            [
                'id' => 1,
                'user_type_id' => 1, // Super Admin
                'name' => 'Admin User',
                'email' => 'admin@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'user_type_id' => 2, // Admin
                'name' => 'John Smith',
                'email' => 'john.smith@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_type_id' => 3, // Financial Admin
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'user_type_id' => 4, // Property manager
                'name' => 'Michael Brown',
                'email' => 'michael.brown@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'user_type_id' => 5, // Office Administrator
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'user_type_id' => 6, // Assistant Property Manager
                'name' => 'David Taylor',
                'email' => 'david.taylor@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'user_type_id' => 4, // Property manager
                'name' => 'Lisa Anderson',
                'email' => 'lisa.anderson@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'user_type_id' => 4, // Property manager
                'name' => 'Robert Garcia',
                'email' => 'robert.garcia@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'user_type_id' => 4, // Property manager
                'name' => 'Jennifer Martinez',
                'email' => 'jennifer.martinez@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'user_type_id' => 6, // Assistant Property Manager
                'name' => 'Christopher Robinson',
                'email' => 'christopher.robinson@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        }
    }
}
