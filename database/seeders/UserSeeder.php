<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@proman.com',
                'password' => Hash::make('password123'),
                'user_role_id' => 1,
            ],
            [
                'name' => 'Manager User',
                'email' => 'manager@proman.com',
                'password' => Hash::make('password123'),
                'user_role_id' => 2,
            ],
            [
                'name' => 'Regular User',
                'email' => 'user@proman.com',
                'password' => Hash::make('password123'),
                'user_role_id' => 3,
            ],
            [
                'name' => 'Contract Manager',
                'email' => 'contract.manager@proman.com',
                'password' => Hash::make('password123'),
                'user_role_id' => 4, // ensure valid role id
            ],
            [
                'name' => 'Contract User',
                'email' => 'contract.user@proman.com',
                'password' => Hash::make('password123'),
                'user_role_id' => 5, // ensure valid role id
            ],
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                array_merge($user, [
                    'avatar' => null,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'updated_at' => now(),
                    'created_at' => now(),
                ])
            );
        }
    }
}
