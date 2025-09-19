<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Scheduled', 'is_updated' => 1],
            ['name' => 'In Progress', 'is_updated' => 1],
            ['name' => 'Completed', 'is_updated' => 1],
            ['name' => 'Cancelled', 'is_updated' => 0],
            ['name' => 'On Hold', 'is_updated' => 1],
            ['name' => 'Rescheduled', 'is_updated' => 1],
        ];

        foreach ($statuses as $status) {
            // Use updateOrInsert to prevent duplicates
            DB::table('job_statuses')->updateOrInsert(
                ['name' => $status['name']], // Search criteria
                [
                    'name' => $status['name'],
                    'is_updated' => $status['is_updated'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
