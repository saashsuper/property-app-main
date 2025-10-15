<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BlockWorkOrder;
use App\Models\User;
use App\Models\Block;
use App\Models\Priority;
use Carbon\Carbon;

class BlockWorkOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find a contractor user or any user
        $user = User::where('email', 'jayadev@proman.com')->first();
        
        // If specific user not found, use any contractor user
        if (!$user) {
            $user = User::where('user_type_id', 7)->first(); // Contractor User
        }
        
        // If still no user, use any user
        if (!$user) {
            $user = User::first();
        }
        
        if (!$user) {
            $this->command->warn('No users found. Skipping BlockWorkOrderSeeder.');
            return;
        }

        // Get some existing blocks
        $blocks = Block::take(5)->get();
        if ($blocks->isEmpty()) {
            $this->command->error('No blocks found. Please seed blocks first.');
            return;
        }

        // Get priorities
        $priorities = Priority::all();
        if ($priorities->isEmpty()) {
            $this->command->error('No priorities found. Please seed priorities first.');
            return;
        }

        // Get some other users to be issued_by
        $otherUsers = User::where('id', '!=', $user->id)->take(3)->get();

        // Create some dummy block issues if none exist
        $blockIssues = [];
        foreach ($blocks as $block) {
            $blockIssues[] = DB::table('block_issues')->insertGetId([
                'ref_no' => 'ISSUE-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'block_id' => $block->id,
                'issue' => 'General Maintenance Issue for ' . $block->name,
                'issue_details' => 'General maintenance work required for this block',
                'issue_status_id' => 1, // Open
                'priority_id' => $priorities->random()->id,
                'reported_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ]);
        }

        $workOrders = [
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 1, // Pending
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Fix leaking faucet in unit 101 kitchen',
                'issued_date_time' => Carbon::now()->subDays(5),
                'preferred_start_date_time' => Carbon::now()->addDays(2),
                'preferred_end_date_time' => Carbon::now()->addDays(3),
                'deadline_date' => Carbon::now()->addDays(7),
                'contact_name' => 'John Smith',
                'contact_mobile' => '+971501234567',
                'contact_email' => 'john.smith@example.com',
                'note_for_access' => 'Please call before arrival. Unit is on 1st floor.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(5),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 2, // In Progress
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Replace broken window in unit 205 living room',
                'issued_date_time' => Carbon::now()->subDays(3),
                'preferred_start_date_time' => Carbon::now()->subDays(1),
                'preferred_end_date_time' => Carbon::now()->addDays(1),
                'deadline_date' => Carbon::now()->addDays(5),
                'contact_name' => 'Sarah Johnson',
                'contact_mobile' => '+971507654321',
                'contact_email' => 'sarah.johnson@example.com',
                'note_for_access' => 'Window is on the balcony side. Bring safety equipment.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 3, // Completed
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Repair air conditioning unit in unit 302',
                'issued_date_time' => Carbon::now()->subDays(10),
                'preferred_start_date_time' => Carbon::now()->subDays(8),
                'preferred_end_date_time' => Carbon::now()->subDays(7),
                'deadline_date' => Carbon::now()->subDays(5),
                'contact_name' => 'Mike Wilson',
                'contact_mobile' => '+971509876543',
                'contact_email' => 'mike.wilson@example.com',
                'note_for_access' => 'AC unit is on the balcony. Work completed successfully.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 4, // Cancelled
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Install new door lock for unit 108',
                'issued_date_time' => Carbon::now()->subDays(7),
                'preferred_start_date_time' => Carbon::now()->subDays(5),
                'preferred_end_date_time' => Carbon::now()->subDays(4),
                'deadline_date' => Carbon::now()->subDays(3),
                'contact_name' => 'Emma Davis',
                'contact_mobile' => '+971501112223',
                'contact_email' => 'emma.davis@example.com',
                'note_for_access' => 'Work cancelled due to tenant moving out.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 5, // On Hold
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Paint walls in unit 156 after water damage repair',
                'issued_date_time' => Carbon::now()->subDays(4),
                'preferred_start_date_time' => Carbon::now()->addDays(1),
                'preferred_end_date_time' => Carbon::now()->addDays(3),
                'deadline_date' => Carbon::now()->addDays(10),
                'contact_name' => 'Robert Brown',
                'contact_mobile' => '+971504445556',
                'contact_email' => 'robert.brown@example.com',
                'note_for_access' => 'Work on hold pending approval from insurance company.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 1, // Pending
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Fix electrical outlet in unit 89 kitchen',
                'issued_date_time' => Carbon::now()->subDays(2),
                'preferred_start_date_time' => Carbon::now()->addDays(1),
                'preferred_end_date_time' => Carbon::now()->addDays(2),
                'deadline_date' => Carbon::now()->addDays(5),
                'contact_name' => 'Lisa Anderson',
                'contact_mobile' => '+971507778889',
                'contact_email' => 'lisa.anderson@example.com',
                'note_for_access' => 'Electrical work - ensure power is turned off before starting.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 2, // In Progress
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Replace damaged tiles in unit 77 bathroom',
                'issued_date_time' => Carbon::now()->subDays(6),
                'preferred_start_date_time' => Carbon::now()->subDays(4),
                'preferred_end_date_time' => Carbon::now()->addDays(1),
                'deadline_date' => Carbon::now()->addDays(3),
                'contact_name' => 'David Miller',
                'contact_mobile' => '+971501234567',
                'contact_email' => 'david.miller@example.com',
                'note_for_access' => 'Bathroom renovation in progress. Tiles need to be ordered.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(1),
            ],
            [
                'block_id' => $blocks->random()->id,
                'block_issue_id' => $blockIssues[array_rand($blockIssues)], // Random block issue
                'contractor_id' => $user->id,
                'issued_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'priority_id' => $priorities->random()->id,
                'status' => 3, // Completed
                'ref_no' => 'WO-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'issue' => 'Install new light fixtures in unit 134 living room',
                'issued_date_time' => Carbon::now()->subDays(12),
                'preferred_start_date_time' => Carbon::now()->subDays(10),
                'preferred_end_date_time' => Carbon::now()->subDays(9),
                'deadline_date' => Carbon::now()->subDays(7),
                'contact_name' => 'Jennifer Taylor',
                'contact_mobile' => '+971509998887',
                'contact_email' => 'jennifer.taylor@example.com',
                'note_for_access' => 'Light fixtures installed successfully. Tenant satisfied.',
                'created_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'updated_by' => $otherUsers->isNotEmpty() ? $otherUsers->random()->id : $user->id,
                'created_at' => Carbon::now()->subDays(12),
                'updated_at' => Carbon::now()->subDays(5),
            ]
        ];

        foreach ($workOrders as $workOrderData) {
            BlockWorkOrder::create($workOrderData);
        }

        $this->command->info('Created ' . count($workOrders) . ' work orders for contractor: ' . $user->email);
        $this->command->info('Work orders created with different statuses:');
        $this->command->info('- Pending: 2 work orders');
        $this->command->info('- In Progress: 2 work orders');
        $this->command->info('- Completed: 2 work orders');
        $this->command->info('- Cancelled: 1 work order');
        $this->command->info('- On Hold: 1 work order');
    }
}

