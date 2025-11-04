<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RealUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates the initial admin user for login
     */
    public function run(): void
    {
        $users = [
            [
                'user_type_id' => 1, // Super Admin
                'name' => 'Admin User',
                'email' => 'admin@proman.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            
            // Assign Super Admin role
            $user->syncRoles(['Super Admin']);
            $this->command->info("✅ Created user: {$user->email} (Super Admin)");
        }
    }
}
