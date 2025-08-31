<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockInspection;
use App\Models\BlockInspectionTeam;
use App\Models\Block;
use App\Models\User;
use Carbon\Carbon;

class BlockInspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = Block::all();
        $users = User::all();

        if ($blocks->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No blocks or users found. Skipping BlockInspection seeder.');
            return;
        }

        $inspectionNotes = [
            'Comprehensive inspection completed. All systems functioning properly.',
            'Minor issues identified and documented for follow-up.',
            'Quality standards exceeded. Excellent workmanship observed.',
            'Some areas require attention. Detailed report provided.',
            'Safety protocols followed. No immediate concerns.',
            'Construction quality meets industry standards.',
            'Timeline on track. Progress satisfactory.',
            'Environmental compliance verified.',
            'Accessibility features properly implemented.',
            'Documentation complete and accurate.'
        ];

        // Create 30 block inspections
        for ($i = 1; $i <= 30; $i++) {
            $block = $blocks->random();
            $user = $users->random();
            
            // Generate reference number
            $refNo = 'BI-' . str_pad($i, 6, '0', STR_PAD_LEFT);
            
            // Random dates within the last 3 months
            $scheduledDate = Carbon::now()->subDays(rand(1, 90))->setTime(rand(8, 17), rand(0, 59));
            
            // 80% of inspections have started
            $hasStarted = rand(1, 100) <= 80;
            $startDateTime = $hasStarted ? $scheduledDate->copy()->addMinutes(rand(-30, 60)) : null;
            
            // 60% of started inspections have ended
            $hasEnded = $hasStarted && rand(1, 100) <= 60;
            $endDateTime = $hasEnded ? $startDateTime->copy()->addHours(rand(2, 12)) : null;
            
            // Determine status based on dates
            $jobStatusId = 1; // Scheduled
            if ($hasStarted && !$hasEnded) {
                $jobStatusId = 2; // In Progress
            } elseif ($hasEnded) {
                $jobStatusId = 3; // Completed
            }

            $inspection = BlockInspection::create([
                'block_id' => $block->id,
                'ref_no' => $refNo,
                'scheduled_date_time' => $scheduledDate,
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
                'notes' => $inspectionNotes[array_rand($inspectionNotes)],
                'job_status_id' => $jobStatusId,
                'is_mobile' => rand(0, 1) == 1, // Random mobile flag
                'created_by' => $user->id,
                'updated_by' => $user->id,
                'created_at' => $scheduledDate->copy()->subDays(rand(1, 7)),
                'updated_at' => $hasEnded ? $endDateTime : ($hasStarted ? $startDateTime : $scheduledDate),
            ]);

            // Create inspection team (1-4 members)
            $teamSize = rand(1, 4);
            $teamMembers = $users->random($teamSize);
            
            foreach ($teamMembers as $index => $teamMember) {
                BlockInspectionTeam::create([
                    'block_inspection_id' => $inspection->id,
                    'user_id' => $teamMember->id,
                    'role' => $index === 0 ? 'Lead Inspector' : 'Inspector',
                    'is_lead' => $index === 0, // First member is lead
                    'created_at' => $inspection->created_at,
                    'updated_at' => $inspection->updated_at,
                ]);
            }
        }

        $this->command->info('BlockInspection seeder completed successfully. Created 30 block inspections with teams.');
    }
}
