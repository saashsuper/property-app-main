<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->insertOrIgnore([
            ['name' => 'Admin', 'description' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'description' => 'Project Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'User', 'description' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contract Manager', 'description' => 'Contract Management Specialist', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Contract User', 'description' => 'Contract User with Limited Access', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
