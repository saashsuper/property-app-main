<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlockContractorTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('block_contractor_types')->truncate();
        DB::table('block_contractor_types')->insert([
            [ 'id' => 1, 'document' => '', 'name' => 'Electrical Repairs', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 2, 'document' => '', 'name' => 'Plumbing Repairs', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 3, 'document' => '', 'name' => 'General Maintenance', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 4, 'document' => '', 'name' => 'Access Control', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 5, 'document' => '', 'name' => 'Gate Maintenance', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 6, 'document' => '', 'name' => 'Tarmacs', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 7, 'document' => '', 'name' => 'Cleaning', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 8, 'document' => '', 'name' => 'Painting', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 9, 'document' => '', 'name' => 'Landscaping', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 10, 'document' => '', 'name' => 'Tree Surgeon', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 11, 'document' => '', 'name' => 'Car Park Management ', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 12, 'document' => '', 'name' => 'Lift Maintenance', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 13, 'document' => '', 'name' => 'Roofing Contractor', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 14, 'document' => '', 'name' => 'Window Cleaning', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 15, 'document' => '', 'name' => 'Gutter Cleaning', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
            [ 'id' => 16, 'document' => '', 'name' => 'Boiler Service', 'common_status_id' => 1, 'deleted_at' => null, 'created_at' => Carbon::parse('2022-09-18 08:24:13'), 'updated_at' => Carbon::parse('2022-09-18 08:24:13') ],
        ]);
    }
}
