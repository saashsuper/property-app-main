<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IssuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Maintenance',
            'Security',
            'Utilities',
            'Infrastructure',
            'Safety',
            'Compliance',
            'Technology',
            'Facilities',
            'Environmental',
            'Administrative'
        ];

        $issueTitles = [
            'Water Leak in Building A',
            'Broken Security Camera',
            'Power Outage in Block 3',
            'Elevator Maintenance Required',
            'Fire Alarm System Check',
            'HVAC System Not Working',
            'Parking Lot Lighting Issue',
            'Garbage Disposal Problem',
            'Internet Connectivity Issue',
            'Emergency Exit Door Stuck',
            'Plumbing Issue in Unit 205',
            'Air Conditioning Failure',
            'Security Gate Malfunction',
            'Pool Maintenance Required',
            'Gym Equipment Repair',
            'Landscaping Issue',
            'Roof Leak in Building B',
            'Electrical Panel Inspection',
            'Sewage Backup Problem',
            'Fire Extinguisher Expired',
            'Window Repair Needed',
            'Heating System Failure',
            'Security System Upgrade',
            'Parking Space Allocation',
            'Common Area Cleaning',
            'Pest Control Required',
            'Building Access Card Issue',
            'Water Heater Replacement',
            'Stairwell Lighting Problem',
            'Mailbox Repair Needed'
        ];

        $issueDescriptions = [
            'There is a significant water leak coming from the ceiling in the main lobby area. Water is pooling on the floor and could cause safety hazards.',
            'Security camera #3 in the parking garage is not functioning properly. The feed is blurry and intermittent.',
            'Complete power outage affecting all units in Block 3. Residents are without electricity for the past 2 hours.',
            'Elevator #2 is making unusual noises and has been stuck between floors twice today. Immediate maintenance required.',
            'Annual fire alarm system inspection is overdue. Need to schedule comprehensive testing of all alarms and sprinklers.',
            'Central HVAC system is not cooling properly. Temperature in common areas is 85°F despite thermostat being set to 72°F.',
            'Several parking lot lights are out, creating dark areas that could pose security risks.',
            'Main garbage disposal unit in the kitchen area is clogged and not functioning.',
            'WiFi connectivity is extremely slow throughout the building. Residents are unable to work from home effectively.',
            'Emergency exit door on the 3rd floor is stuck and cannot be opened. This is a serious safety concern.',
            'Unit 205 reports a plumbing issue with the kitchen sink. Water is backing up and not draining properly.',
            'Air conditioning unit in the fitness center has completely failed. Room temperature is unbearable.',
            'Main security gate is not responding to access cards. Residents are having difficulty entering the property.',
            'Pool water is cloudy and chemical levels are off. Pool maintenance and cleaning required.',
            'Treadmill #2 in the gym is broken and unsafe to use. Belt is slipping and motor is making loud noises.',
            'Several trees in the courtyard need trimming. Branches are hanging low and could damage property.',
            'Roof leak in Building B is causing water damage to ceiling tiles in the hallway.',
            'Electrical panel in the basement needs inspection. Circuit breakers are tripping frequently.',
            'Sewage backup in the main line is causing unpleasant odors throughout the building.',
            'Fire extinguisher in the lobby has expired and needs replacement.',
            'Window in unit 312 has a crack and needs immediate repair for security and weather protection.',
            'Heating system in the community room is not working. Room is too cold for residents to use.',
            'Security system needs software upgrade to fix known vulnerabilities.',
            'Parking space allocation needs review. Several residents are complaining about space assignments.',
            'Common area carpets need deep cleaning. Stains and odors are present.',
            'Pest control treatment needed. Residents are reporting sightings of rodents.',
            'Building access card system is malfunctioning. Cards are not being recognized.',
            'Water heater in the laundry room needs replacement. Water is not getting hot enough.',
            'Stairwell lighting on floors 2-4 is not working. Safety hazard for residents using stairs.',
            'Mailbox cluster needs repair. Several mailboxes are damaged and not secure.'
        ];

        $locations = [
            'Building A - Lobby',
            'Building A - Parking Garage',
            'Block 3 - All Units',
            'Building B - Elevator #2',
            'Building A - Fire Alarm System',
            'Building A - HVAC Room',
            'Main Parking Lot',
            'Building A - Kitchen',
            'Building A - WiFi Network',
            'Building B - 3rd Floor',
            'Building A - Unit 205',
            'Building A - Fitness Center',
            'Main Security Gate',
            'Building A - Pool Area',
            'Building A - Gym',
            'Building A - Courtyard',
            'Building B - Roof',
            'Building A - Basement',
            'Building A - Main Sewer Line',
            'Building A - Lobby',
            'Building B - Unit 312',
            'Building A - Community Room',
            'Building A - Security Office',
            'Main Parking Area',
            'Building A - Common Areas',
            'Building A - All Units',
            'Building A - Access Control',
            'Building A - Laundry Room',
            'Building A - Stairwells',
            'Building A - Mailbox Area'
        ];

        $issues = [];

        for ($i = 1; $i <= 30; $i++) {
            $priority = rand(1, 5); // 1=Low, 2=Normal, 3=High, 4=Urgent, 5=Critical
            $status = rand(1, 5); // 1=Open, 2=In Progress, 3=Resolved, 4=Closed, 5=On Hold
            $category = $categories[array_rand($categories)];
            $title = $issueTitles[array_rand($issueTitles)];
            $description = $issueDescriptions[array_rand($issueDescriptions)];
            $location = $locations[array_rand($locations)];
            
            // Generate unique reference number
            $refNo = 'ISS-' . date('Y') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            $issues[] = [
                'ref_no' => $refNo,
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'priority' => $priority,
                'status' => $status,
                'assigned_to' => rand(1, 3), // Assuming we have users with IDs 1-3
                'reported_by' => rand(1, 3),
                'location' => $location,
                'created_by' => 1, // Admin user
                'updated_by' => rand(1, 3),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(0, 15)),
            ];
        }

        foreach ($issues as $issue) {
            DB::table('issues')->insert($issue);
        }
    }
} 