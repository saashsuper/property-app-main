<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name' => 'Call Out'],
            ['name' => 'Meter Reading'],
            ['name' => 'Emergency'],
            ['name' => 'Memo Drop'],
            ['name' => 'Routine Inspection'],
            ['name' => 'Maintenance'],
            ['name' => 'Repair'],
            ['name' => 'Installation'],
            ['name' => 'Assessment'],
            ['name' => 'Follow Up'],
        ];

        foreach ($reasons as $reason) {
            DB::table('job_reasons')->updateOrInsert(
                ['name' => $reason['name']],
                [
                    'name' => $reason['name'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
