<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\User;
use App\Models\Country;
use App\Models\State;

class TestBlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test block type if it doesn't exist
        $blockType = BlockType::firstOrCreate(
            ['name' => 'Residential'],
            ['name' => 'Residential']
        );

        // Create a test user if it doesn't exist
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create a test country if it doesn't exist
        $country = Country::firstOrCreate(
            ['country_name' => 'United States'],
            ['country_name' => 'United States']
        );

        // Create a test state if it doesn't exist
        $state = State::firstOrCreate(
            ['name' => 'California'],
            ['name' => 'California', 'country_id' => $country->id]
        );

        // Create a test block
        Block::firstOrCreate(
            ['name' => 'Test Block'],
            [
                'name' => 'Test Block',
                'management_company' => 'Test Management Co.',
                'block_type_id' => $blockType->id,
                'user_id' => $user->id,
                'address1' => '123 Test Street',
                'country_id' => $country->id,
                'state_id' => $state->id,
                'car_spaces' => 10,
                'inspection_count' => 4,
                'no_of_units' => 20,
                'created_by' => $user->id,
            ]
        );

        $this->command->info('Test block created successfully!');
    }
}
