<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contactMethods = [
            [
                'id' => 1,
                'name' => 'Phone - Office',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 2,
                'name' => 'Phone - After Hours',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 3,
                'name' => 'Email',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 4,
                'name' => 'In Person',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 5,
                'name' => 'Site Visit',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 6,
                'name' => 'Block Inspection',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ],
            [
                'id' => 7,
                'name' => 'Meetings',
                'created_at' => '2022-09-18 08:24:13',
                'updated_at' => '2022-09-18 08:24:13'
            ]
        ];

        foreach ($contactMethods as $method) {
            DB::table('contact_methods')->insertOrIgnore($method);
        }

        $this->command->info('ContactMethodsSeeder completed successfully. Created ' . count($contactMethods) . ' contact methods.');
    }
}
