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
                'name' => 'system@proman.com',
                'email' => 'system@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($users as $user) {
            \App\Models\User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
