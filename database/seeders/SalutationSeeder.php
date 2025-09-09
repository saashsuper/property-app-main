<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Salutation;

class SalutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salutations = [
            ['name' => 'Mr', 'common_status_id' => 1],
            ['name' => 'Ms', 'common_status_id' => 1],
            ['name' => 'Miss', 'common_status_id' => 1],
            ['name' => 'Mrs', 'common_status_id' => 1],
            ['name' => 'Dr', 'common_status_id' => 1],
            ['name' => 'Prof', 'common_status_id' => 1],
            ['name' => 'Dear Sirs', 'common_status_id' => 1],
            ['name' => 'Dear Madam', 'common_status_id' => 1],
            ['name' => 'Dear Sir', 'common_status_id' => 1],
            ['name' => 'To Whom It May Concern', 'common_status_id' => 1],
        ];

        foreach ($salutations as $salutation) {
            Salutation::updateOrCreate(
                ['name' => $salutation['name']],
                $salutation
            );
        }
    }
}
