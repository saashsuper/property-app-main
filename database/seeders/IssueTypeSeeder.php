<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IssueType;

class IssueTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $issueTypes = [
            [
                'name' => 'plumbing',
                'description' => 'Plumbing related issues',
                'is_active' => true
            ],
            [
                'name' => 'electrical',
                'description' => 'Electrical related issues',
                'is_active' => true
            ],
            [
                'name' => 'hvac',
                'description' => 'HVAC (Heating, Ventilation, and Air Conditioning) related issues',
                'is_active' => true
            ],
            [
                'name' => 'structural',
                'description' => 'Structural related issues',
                'is_active' => true
            ],
            [
                'name' => 'security',
                'description' => 'Security related issues',
                'is_active' => true
            ],
            [
                'name' => 'fire_safety',
                'description' => 'Fire safety related issues',
                'is_active' => true
            ],
            [
                'name' => 'other',
                'description' => 'Other miscellaneous issues',
                'is_active' => true
            ]
        ];

        foreach ($issueTypes as $issueType) {
            IssueType::firstOrCreate(
                ['name' => $issueType['name']],
                $issueType
            );
        }
    }
}
