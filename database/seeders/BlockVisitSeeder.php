<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockVisit;
use App\Models\Block;
use App\Models\User;
use Carbon\Carbon;

class BlockVisitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = Block::all();
        $users = User::all();

        if ($blocks->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No blocks or users found. Skipping BlockVisit seeder.');
            return;
        }

        $visitNotes = [
            'All safety protocols followed. No issues identified.',
            'Minor maintenance required on HVAC systems.',
            'Construction progressing according to schedule.',
            'Quality standards met. Ready for next phase.',
            'Some delays due to weather conditions.',
            'Excellent work quality. Team performing well.',
            'Minor adjustments needed for compliance.',
            'Site conditions good. No immediate concerns.',
            'Contractor coordination effective.',
            'Documentation complete and up to date.'
        ];

        $visitComments = [
            'Site visit completed successfully. All safety protocols were followed and no issues were identified during the inspection.',
            'Minor maintenance required on HVAC systems. Contractor has been notified and will address within the week.',
            'Construction is progressing according to schedule. Quality standards are being maintained throughout the project.',
            'Quality standards have been met. The site is ready for the next phase of construction.',
            'Some delays due to weather conditions. Project timeline has been adjusted accordingly.',
            'Excellent work quality observed. The team is performing well and maintaining high standards.',
            'Minor adjustments needed for compliance. All issues have been documented and will be addressed.',
            'Site conditions are good with no immediate concerns. Regular monitoring will continue.',
            'Contractor coordination is effective. Communication channels are working well.',
            'Documentation is complete and up to date. All permits and approvals are in order.'
        ];

        // Create 50 block visits
        for ($i = 1; $i <= 50; $i++) {
            $block = $blocks->random();
            $user = $users->random();
            
            // Generate reference number
            $refNo = 'BV-' . str_pad($i, 6, '0', STR_PAD_LEFT);
            
            // Random dates within the last 6 months
            $scheduledDate = Carbon::now()->subDays(rand(1, 180))->setTime(rand(8, 17), rand(0, 59));
            
            // 70% of visits have started
            $hasStarted = rand(1, 100) <= 70;
            $startDateTime = $hasStarted ? $scheduledDate->copy()->addMinutes(rand(-30, 60)) : null;
            
            // 50% of started visits have ended
            $hasEnded = $hasStarted && rand(1, 100) <= 50;
            $endDateTime = $hasEnded ? $startDateTime->copy()->addHours(rand(1, 8)) : null;
            
            // Determine status based on dates
            $jobStatusId = 1; // Pending
            if ($hasStarted && !$hasEnded) {
                $jobStatusId = 2; // In Progress
            } elseif ($hasEnded) {
                $jobStatusId = 3; // Completed
            }

            BlockVisit::create([
                'block_id' => $block->id,
                'ref_no' => $refNo,
                'scheduled_date_time' => $scheduledDate,
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'job_reason_id' => rand(1, 5), // Random reason ID
                'notes' => $visitNotes[array_rand($visitNotes)],
                'comment' => $visitComments[array_rand($visitComments)],
                'job_status_id' => $jobStatusId,
                'block_visit_action_id' => rand(1, 3), // Random action ID
                'is_mobile' => rand(0, 1) == 1, // Random mobile flag
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $scheduledDate->copy()->subDays(rand(1, 7)),
                'updated_at' => $hasEnded ? $endDateTime : ($hasStarted ? $startDateTime : $scheduledDate),
            ]);
        }

        $this->command->info('BlockVisit seeder completed successfully. Created 50 block visits.');
    }
}
