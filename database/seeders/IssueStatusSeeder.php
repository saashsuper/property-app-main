<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueStatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Open', 'value' => 1, 'btn_class' => 'warning', 'description' => 'Issue has been reported and is awaiting attention'],
            ['label' => 'In Progress', 'value' => 2, 'btn_class' => 'info', 'description' => 'Issue is currently being worked on'],
            ['label' => 'Resolved', 'value' => 3, 'btn_class' => 'success', 'description' => 'Issue has been resolved successfully'],
            ['label' => 'Closed', 'value' => 4, 'btn_class' => 'secondary', 'description' => 'Issue has been closed and documented'],
            ['label' => 'On Hold', 'value' => 5, 'btn_class' => 'danger', 'description' => 'Issue is temporarily on hold'],
        ];

        foreach ($rows as $row) {
            DB::table('issue_statuses')->updateOrInsert(
                ['value' => $row['value']],
                $row + ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}

