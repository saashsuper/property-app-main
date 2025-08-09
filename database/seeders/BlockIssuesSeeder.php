<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlockIssuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing blocks and users
        $blocks = Block::all();
        $users = User::all();

        if ($blocks->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No blocks or users found. Please run BlockSeeder and UserSeeder first.');
            return;
        }

        $issueTypes = [
            'Plumbing Issue',
            'Electrical Problem',
            'HVAC Maintenance',
            'Structural Damage',
            'Security Concern',
            'Fire Safety Issue',
            'Water Leakage',
            'Noise Complaint',
            'Parking Issue',
            'Landscaping Problem',
            'Elevator Malfunction',
            'Internet Connectivity',
            'Trash Collection',
            'Lighting Issue',
            'Access Control Problem'
        ];

        $issueDetails = [
            'Urgent attention required for this maintenance issue.',
            'Regular maintenance check needed.',
            'Minor issue that needs to be addressed.',
            'Critical safety concern that requires immediate action.',
            'Standard maintenance request.',
            'Emergency repair needed.',
            'Preventive maintenance required.',
            'Tenant complaint that needs resolution.',
            'Infrastructure improvement needed.',
            'Safety compliance issue.'
        ];

        // Create 50 sample issues
        for ($i = 1; $i <= 50; $i++) {
            $block = $blocks->random();
            $user = $users->random();
            
            // Random date within last 30 days
            $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            
            BlockIssue::create([
                'ref_no' => 'ISSUE-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'block_id' => $block->id,
                'issued_from' => rand(1, 3),
                'from_id' => rand(1, 10),
                'contractor_type_id' => rand(1, 5),
                'priority_id' => rand(1, 5), // 1=Low, 2=Normal, 3=High, 4=Urgent, 5=Critical
                'block_unit_id' => null, // Can be null for block-level issues
                'contact_details' => 'Contact details for issue ' . $i,
                'issue' => $issueTypes[array_rand($issueTypes)],
                'issue_details' => $issueDetails[array_rand($issueDetails)],
                'contact_name' => 'Contact Person ' . $i,
                'contact_mobile' => '+1' . rand(1000000000, 9999999999),
                'contact_email' => 'contact' . $i . '@proman.com',
                'preferred_start_date_time' => $createdAt->addDays(rand(1, 7)),
                'preferred_end_date_time' => $createdAt->addDays(rand(8, 14)),
                'note_for_access' => 'Access note for issue ' . $i,
                'issued_by' => $user->id,
                'block_visit_id' => null,
                'block_inspection_id' => null,
                'issued_date_time' => $createdAt,
                'comment' => 'Additional comments for issue ' . $i,
                'is_mobile' => rand(0, 1),
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'issue_status_id' => rand(1, 5), // 1=Open, 2=In Progress, 3=Resolved, 4=Closed, 5=On Hold
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Block Issues seeded successfully!');
    }
} 