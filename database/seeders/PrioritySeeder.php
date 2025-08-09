<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrioritySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['label' => 'Low', 'value' => 1, 'btn_class' => 'success'],
            ['label' => 'Normal', 'value' => 2, 'btn_class' => 'info'],
            ['label' => 'High', 'value' => 3, 'btn_class' => 'warning'],
            ['label' => 'Urgent', 'value' => 4, 'btn_class' => 'danger'],
            ['label' => 'Critical', 'value' => 5, 'btn_class' => 'dark'],
        ];

        foreach ($rows as $row) {
            DB::table('priorities')->updateOrInsert(
                ['value' => $row['value']],
                $row + ['updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}


