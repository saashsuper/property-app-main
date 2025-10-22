<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign Super Admin role to the first user (or create one)
        $superAdminUser = User::first();
        
        if ($superAdminUser) {
            $superAdminUser->assignRole('Super Admin');
            $this->command->info("Super Admin role assigned to user: {$superAdminUser->email}");
        } else {
            $this->command->warn('No users found. Please create a user first.');
        }

        // You can assign roles to other specific users here
        // Example:
        // $user = User::where('email', 'manager@example.com')->first();
        // if ($user) {
        //     $user->assignRole('Manager');
        // }
    }
}

