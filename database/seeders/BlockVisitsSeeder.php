<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlockVisitsSeeder extends Seeder
{
    public function run(): void
    {
        $blockIds = DB::table('blocks')->pluck('id');
        if ($blockIds->isEmpty()) {
            $this->command?->warn('Skipping BlockVisitsSeeder: no blocks found');
            return;
        }

        $now = now();
        $rows = [];
        foreach ($blockIds as $blockId) {
            for ($i = 1; $i <= 5; $i++) {
                $scheduled = $now->copy()->subDays(rand(0, 15))->setTime(rand(8, 16), [0, 15, 30, 45][array_rand([0,1,2,3])]);
                $start = (clone $scheduled)->addMinutes(rand(0, 60));
                $end = (clone $start)->addMinutes(rand(30, 180));
                $rows[] = [
                    'block_id' => $blockId,
                    'ref_no' => 'SV-' . strtoupper(Str::random(6)),
                    'scheduled_date_time' => $scheduled,
                    'start_date_time' => $start,
                    'end_date_time' => $end,
                    'job_reason_id' => null,
                    'notes' => 'Routine site visit #' . $i,
                    'comment' => null,
                    'pdf_path' => null,
                    'pdf_name' => null,
                    'job_status_id' => null,
                    'block_visit_action_id' => null,
                    'is_mobile' => false,
                    'created_by' => 1,
                    'updated_by' => 1,
                    'deleted_by' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('block_visits')->insert($chunk);
        }
    }
}


