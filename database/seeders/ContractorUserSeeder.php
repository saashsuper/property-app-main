<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ContractorUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add 4 dummy contractor users (only if they don't exist)
        $contractorEmails = [
            'contractor1@proman.com',
            'contractor2@proman.com', 
            'contractor3@proman.com',
            'contractor4@proman.com'
        ];
        
        foreach ($contractorEmails as $index => $email) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Contractor ' . ($index + 1),
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'user_type_id' => 8, // Contractor User type has ID 8
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        
        echo "Contractor users seeded successfully!\n";
    }
}
