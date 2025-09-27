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
        // First, remove the Assistant Property Manager user type if it exists
        \App\Models\UserType::where('name', 'Assistant Property Manager')->delete();
        
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
                'name' => 'Contractor Admin',
                'description' => 'Contractor administrator with management capabilities for contractor operations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'name' => 'Contractor User',
                'description' => 'Contractor user with limited access for contract-related tasks',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($userTypes as $userType) {
            // Use Eloquent model to handle soft deletes properly
            // First check if a user type with this name already exists at a different ID
            $existingByName = \App\Models\UserType::where('name', $userType['name'])->where('id', '!=', $userType['id'])->first();
            if ($existingByName) {
                // If it exists at a different ID, update the existing one to have the new ID
                $existingByName->update(['id' => $userType['id']]);
                // Then update with the new data
                \App\Models\UserType::where('id', $userType['id'])->update([
                    'name' => $userType['name'],
                    'description' => $userType['description'],
                    'is_hidden' => $userType['is_hidden'] ?? false,
                    'updated_at' => $userType['updated_at']
                ]);
            } else {
                // Create or update normally
                \App\Models\UserType::withTrashed()->updateOrCreate(
                    ['id' => $userType['id']],
                    $userType
                );
            }
        }
    }
}
