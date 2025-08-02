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
        DB::table('user_roles')->insert([
            ['name' => 'Admin', 'description' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'description' => 'Project Manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'User', 'description' => 'Regular User', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
