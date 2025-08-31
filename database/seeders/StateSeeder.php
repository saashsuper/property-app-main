<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get country IDs
        $usId = Country::where('country_code', 'US')->first()->id;
        $caId = Country::where('country_code', 'CA')->first()->id;
        $gbId = Country::where('country_code', 'GB')->first()->id;
        $auId = Country::where('country_code', 'AU')->first()->id;
        $inId = Country::where('country_code', 'IN')->first()->id;

        $states = [
            // United States
            ['name' => 'Alabama', 'country_id' => $usId],
            ['name' => 'Alaska', 'country_id' => $usId],
            ['name' => 'Arizona', 'country_id' => $usId],
            ['name' => 'Arkansas', 'country_id' => $usId],
            ['name' => 'California', 'country_id' => $usId],
            ['name' => 'Colorado', 'country_id' => $usId],
            ['name' => 'Connecticut', 'country_id' => $usId],
            ['name' => 'Delaware', 'country_id' => $usId],
            ['name' => 'Florida', 'country_id' => $usId],
            ['name' => 'Georgia', 'country_id' => $usId],
            ['name' => 'Hawaii', 'country_id' => $usId],
            ['name' => 'Idaho', 'country_id' => $usId],
            ['name' => 'Illinois', 'country_id' => $usId],
            ['name' => 'Indiana', 'country_id' => $usId],
            ['name' => 'Iowa', 'country_id' => $usId],
            ['name' => 'Kansas', 'country_id' => $usId],
            ['name' => 'Kentucky', 'country_id' => $usId],
            ['name' => 'Louisiana', 'country_id' => $usId],
            ['name' => 'Maine', 'country_id' => $usId],
            ['name' => 'Maryland', 'country_id' => $usId],
            ['name' => 'Massachusetts', 'country_id' => $usId],
            ['name' => 'Michigan', 'country_id' => $usId],
            ['name' => 'Minnesota', 'country_id' => $usId],
            ['name' => 'Mississippi', 'country_id' => $usId],
            ['name' => 'Missouri', 'country_id' => $usId],
            ['name' => 'Montana', 'country_id' => $usId],
            ['name' => 'Nebraska', 'country_id' => $usId],
            ['name' => 'Nevada', 'country_id' => $usId],
            ['name' => 'New Hampshire', 'country_id' => $usId],
            ['name' => 'New Jersey', 'country_id' => $usId],
            ['name' => 'New Mexico', 'country_id' => $usId],
            ['name' => 'New York', 'country_id' => $usId],
            ['name' => 'North Carolina', 'country_id' => $usId],
            ['name' => 'North Dakota', 'country_id' => $usId],
            ['name' => 'Ohio', 'country_id' => $usId],
            ['name' => 'Oklahoma', 'country_id' => $usId],
            ['name' => 'Oregon', 'country_id' => $usId],
            ['name' => 'Pennsylvania', 'country_id' => $usId],
            ['name' => 'Rhode Island', 'country_id' => $usId],
            ['name' => 'South Carolina', 'country_id' => $usId],
            ['name' => 'South Dakota', 'country_id' => $usId],
            ['name' => 'Tennessee', 'country_id' => $usId],
            ['name' => 'Texas', 'country_id' => $usId],
            ['name' => 'Utah', 'country_id' => $usId],
            ['name' => 'Vermont', 'country_id' => $usId],
            ['name' => 'Virginia', 'country_id' => $usId],
            ['name' => 'Washington', 'country_id' => $usId],
            ['name' => 'West Virginia', 'country_id' => $usId],
            ['name' => 'Wisconsin', 'country_id' => $usId],
            ['name' => 'Wyoming', 'country_id' => $usId],

            // Canada
            ['name' => 'Alberta', 'country_id' => $caId],
            ['name' => 'British Columbia', 'country_id' => $caId],
            ['name' => 'Manitoba', 'country_id' => $caId],
            ['name' => 'New Brunswick', 'country_id' => $caId],
            ['name' => 'Newfoundland and Labrador', 'country_id' => $caId],
            ['name' => 'Nova Scotia', 'country_id' => $caId],
            ['name' => 'Ontario', 'country_id' => $caId],
            ['name' => 'Prince Edward Island', 'country_id' => $caId],
            ['name' => 'Quebec', 'country_id' => $caId],
            ['name' => 'Saskatchewan', 'country_id' => $caId],
            ['name' => 'Northwest Territories', 'country_id' => $caId],
            ['name' => 'Nunavut', 'country_id' => $caId],
            ['name' => 'Yukon', 'country_id' => $caId],

            // United Kingdom
            ['name' => 'England', 'country_id' => $gbId],
            ['name' => 'Scotland', 'country_id' => $gbId],
            ['name' => 'Wales', 'country_id' => $gbId],
            ['name' => 'Northern Ireland', 'country_id' => $gbId],

            // Australia
            ['name' => 'New South Wales', 'country_id' => $auId],
            ['name' => 'Victoria', 'country_id' => $auId],
            ['name' => 'Queensland', 'country_id' => $auId],
            ['name' => 'Western Australia', 'country_id' => $auId],
            ['name' => 'South Australia', 'country_id' => $auId],
            ['name' => 'Tasmania', 'country_id' => $auId],
            ['name' => 'Australian Capital Territory', 'country_id' => $auId],
            ['name' => 'Northern Territory', 'country_id' => $auId],

            // India (Major States)
            ['name' => 'Andhra Pradesh', 'country_id' => $inId],
            ['name' => 'Arunachal Pradesh', 'country_id' => $inId],
            ['name' => 'Assam', 'country_id' => $inId],
            ['name' => 'Bihar', 'country_id' => $inId],
            ['name' => 'Chhattisgarh', 'country_id' => $inId],
            ['name' => 'Goa', 'country_id' => $inId],
            ['name' => 'Gujarat', 'country_id' => $inId],
            ['name' => 'Haryana', 'country_id' => $inId],
            ['name' => 'Himachal Pradesh', 'country_id' => $inId],
            ['name' => 'Jharkhand', 'country_id' => $inId],
            ['name' => 'Karnataka', 'country_id' => $inId],
            ['name' => 'Kerala', 'country_id' => $inId],
            ['name' => 'Madhya Pradesh', 'country_id' => $inId],
            ['name' => 'Maharashtra', 'country_id' => $inId],
            ['name' => 'Manipur', 'country_id' => $inId],
            ['name' => 'Meghalaya', 'country_id' => $inId],
            ['name' => 'Mizoram', 'country_id' => $inId],
            ['name' => 'Nagaland', 'country_id' => $inId],
            ['name' => 'Odisha', 'country_id' => $inId],
            ['name' => 'Punjab', 'country_id' => $inId],
            ['name' => 'Rajasthan', 'country_id' => $inId],
            ['name' => 'Sikkim', 'country_id' => $inId],
            ['name' => 'Tamil Nadu', 'country_id' => $inId],
            ['name' => 'Telangana', 'country_id' => $inId],
            ['name' => 'Tripura', 'country_id' => $inId],
            ['name' => 'Uttar Pradesh', 'country_id' => $inId],
            ['name' => 'Uttarakhand', 'country_id' => $inId],
            ['name' => 'West Bengal', 'country_id' => $inId],
        ];

        foreach ($states as $state) {
            State::create($state);
        }

        $this->command->info('State seeder completed successfully. Created ' . count($states) . ' states.');
    }
}
