<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\State;
use App\Models\Country;

class CompleteStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get country IDs by country code
        $countries = Country::all()->keyBy('country_code');
        
        $states = [
            // United States (US)
            ['name' => 'Alabama', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Alaska', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Arizona', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Arkansas', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'California', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Colorado', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Connecticut', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Delaware', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Florida', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Georgia', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Hawaii', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Idaho', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Illinois', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Indiana', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Iowa', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Kansas', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Kentucky', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Louisiana', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Maine', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Maryland', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Massachusetts', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Michigan', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Minnesota', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Mississippi', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Missouri', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Montana', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Nebraska', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Nevada', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'New Hampshire', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'New Jersey', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'New Mexico', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'New York', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'North Carolina', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'North Dakota', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Ohio', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Oklahoma', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Oregon', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Pennsylvania', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Rhode Island', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'South Carolina', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'South Dakota', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Tennessee', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Texas', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Utah', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Vermont', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Virginia', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Washington', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'West Virginia', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Wisconsin', 'country_id' => $countries['US']->id ?? null],
            ['name' => 'Wyoming', 'country_id' => $countries['US']->id ?? null],

            // Canada (CA)
            ['name' => 'Alberta', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'British Columbia', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Manitoba', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'New Brunswick', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Newfoundland and Labrador', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Nova Scotia', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Ontario', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Prince Edward Island', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Quebec', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Saskatchewan', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Northwest Territories', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Nunavut', 'country_id' => $countries['CA']->id ?? null],
            ['name' => 'Yukon', 'country_id' => $countries['CA']->id ?? null],

            // United Kingdom (GB)
            ['name' => 'England', 'country_id' => $countries['GB']->id ?? null],
            ['name' => 'Scotland', 'country_id' => $countries['GB']->id ?? null],
            ['name' => 'Wales', 'country_id' => $countries['GB']->id ?? null],
            ['name' => 'Northern Ireland', 'country_id' => $countries['GB']->id ?? null],

            // Australia (AU)
            ['name' => 'New South Wales', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Victoria', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Queensland', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Western Australia', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'South Australia', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Tasmania', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Australian Capital Territory', 'country_id' => $countries['AU']->id ?? null],
            ['name' => 'Northern Territory', 'country_id' => $countries['AU']->id ?? null],

            // India (IN) - Complete list
            ['name' => 'Andaman and Nicobar Islands', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Andhra Pradesh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Arunachal Pradesh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Assam', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Bihar', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Chandigarh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Chhattisgarh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Dadra and Nagar Haveli', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Daman and Diu', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Delhi', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Goa', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Gujarat', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Haryana', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Himachal Pradesh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Jammu and Kashmir', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Jharkhand', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Karnataka', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Kerala', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Lakshadweep', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Madhya Pradesh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Maharashtra', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Manipur', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Meghalaya', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Mizoram', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Nagaland', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Odisha', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Pondicherry', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Punjab', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Rajasthan', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Sikkim', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Tamil Nadu', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Telangana', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Tripura', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Uttar Pradesh', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'Uttarakhand', 'country_id' => $countries['IN']->id ?? null],
            ['name' => 'West Bengal', 'country_id' => $countries['IN']->id ?? null],

            // Ireland (IE)
            ['name' => 'Carlow', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Cavan', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Clare', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Cork', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Donegal', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Dublin', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Galway', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Kerry', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Kildare', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Kilkenny', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Laois', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Leitrim', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Limerick', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Longford', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Louth', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Mayo', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Meath', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Monaghan', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Offaly', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Roscommon', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Sligo', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Tipperary', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Waterford', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Westmeath', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Wexford', 'country_id' => $countries['IE']->id ?? null],
            ['name' => 'Wicklow', 'country_id' => $countries['IE']->id ?? null],

            // New Zealand (NZ)
            ['name' => 'Auckland', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Bay of Plenty', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Canterbury', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Gisborne', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Hawke\'s Bay', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Manawatu-Wanganui', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Marlborough', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Nelson', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Northland', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Otago', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Southland', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Taranaki', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Tasman', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Waikato', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Wellington', 'country_id' => $countries['NZ']->id ?? null],
            ['name' => 'Westland', 'country_id' => $countries['NZ']->id ?? null],
        ];

        // Filter out states with null country_id and create them
        $validStates = array_filter($states, function($state) {
            return $state['country_id'] !== null;
        });

        foreach ($validStates as $state) {
            State::updateOrCreate(
                ['name' => $state['name'], 'country_id' => $state['country_id']],
                $state
            );
        }

        $this->command->info('Complete State seeder completed successfully. Created/Updated ' . count($validStates) . ' states.');
    }
}
