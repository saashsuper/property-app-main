<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Clear existing data first
        $this->clearExistingData();
        
        // Seed reference data first
        $this->call([
            CountrySeeder::class,
            CompleteStateSeeder::class,
            UserTypeSeeder::class,
            ContactMethodsSeeder::class,
            SalutationSeeder::class,
            BlockTypeSeeder::class,
            BlockInformationTypeSeeder::class,
            BlockContractorTypeSeeder::class,
            BlockBuildingTypeSeeder::class,
            BlockUnitTypeSeeder::class,
            BlockInspectionValueTypeSeeder::class,
            BlockInspectionValueSeeder::class,
            BuildingAssetSeeder::class,
            JobReasonSeeder::class,
            JobStatusSeeder::class,
            IssueStatusSeeder::class,
            IssueTypeSeeder::class,
        ]);

        // Seed main data
        $this->call([
            RealUserSeeder::class,
            ContractorUserSeeder::class,
            RealBlockSeeder::class,
            BlockBuildingsSeeder::class,
            BlockUnitsSeeder::class,
            BlockVisitSeeder::class,
            BlockInspectionSeeder::class,
        ]);
    }

    /**
     * Clear existing data from tables
     */
    private function clearExistingData(): void
    {
        // Clear data in reverse dependency order
        $tablesToTruncate = [
            'block_inspection_teams',
            'block_inspection_assets', 
            'block_inspection_results',
            'block_inspections',
            'block_visit_teams',
            'block_visit_results',
            'block_visit_images',
            'block_visits',
            'block_units',
            'block_buildings',
            'blocks',
            'building_type_assets',
            'building_assets',
            'building_types',
            'block_inspection_values',
            'block_inspection_value_types',
            'block_unit_types',
            'block_types',
            'issue_statuses',
            'user_types',
            'states',
            'countries',
        ];

        foreach ($tablesToTruncate as $table) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->delete();
                }
            } catch (\Exception $e) {
                // Skip if table doesn't exist or can't be deleted
                continue;
            }
        }
    }
}
