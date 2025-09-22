<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IssueStatusSeeder extends Seeder
{
    public function run(): void
    {
        // Based on saashmagna.sql data - mapping original 'name' to 'label' and 'class' to 'btn_class'
        $rows = [
            ['label' => 'Created', 'value' => 1, 'btn_class' => 'bg-warning', 'description' => 'Issue has been created and is awaiting attention'],
            ['label' => 'In Progress', 'value' => 2, 'btn_class' => 'bg-primary', 'description' => 'Issue is currently being worked on'],
            ['label' => 'Work Order', 'value' => 3, 'btn_class' => 'bg-secondary', 'description' => 'Issue has been converted to a work order'],
            ['label' => 'Completed', 'value' => 4, 'btn_class' => 'bg-success', 'description' => 'Issue has been completed successfully'],
            ['label' => 'Invoiced', 'value' => 5, 'btn_class' => 'bg-light text-dark', 'description' => 'Issue has been invoiced and closed'],
        ];

        foreach ($rows as $row) {
            DB::table('issue_statuses')->updateOrInsert(
                ['value' => $row['value']],
                $row + ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}

