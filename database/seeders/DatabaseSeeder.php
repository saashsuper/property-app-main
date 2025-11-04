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
            BuildingTypeSeeder::class,  // Added: Building types
            BlockInformationTypeSeeder::class,
            BlockContractorTypeSeeder::class,
            BlockBuildingTypeSeeder::class,
            BlockUnitTypeSeeder::class,
            BlockInspectionValueTypeSeeder::class,
            BlockInspectionValueSeeder::class,
            BuildingAssetSeeder::class,
            BuildingTypeAssetSeeder::class,
            BlockBuildingAssetSeeder::class,
            BlockGeneralAssetsSeeder::class,
            JobReasonSeeder::class,
            JobStatusSeeder::class,
            IssueStatusSeeder::class,
            IssueTypeSeeder::class,
            PrioritySeeder::class,
            PermissionsSeeder::class,  // Added: Permissions and roles
            RealUserSeeder::class,  // Create admin user with proper role assignment
        ]);

        // Seed main data
        $this->call([
            // RoleUserSeeder::class,  // Added: Assign roles to users
            // ContractorUserSeeder::class,
            // RealBlockSeeder::class,
            // BlockBuildingsSeeder::class,
            // BlockUnitsSeeder::class,
            // BlockVisitSeeder::class,
            // BlockVisitImageSeeder::class,
            // BlockInspectionSeeder::class,
            // BlockInspectionAssetImageSeeder::class,
            // BlockIssuesSeeder::class,
            // BlockWorkOrderSeeder::class,
        ]);
    }

    /**
     * Clear existing data from tables
     */
    private function clearExistingData(): void
    {
        // Clear data in reverse dependency order
        $tablesToTruncate = [
            'block_work_order_images',
            'block_work_orders',
            'block_issue_images',
            'block_issue_actions',
            'issue_logs',
            'block_issues',
            'block_inspection_teams',
            'block_inspection_asset_images',
            'block_inspection_assets', 
            'block_inspection_results',
            'block_inspections',
            'block_visit_teams',
            'block_visit_results',
            'block_visit_images',
            'block_visits',
            'block_units',
            'block_buildings',
            'block_general_assets',
            'blocks',
            'building_type_assets',
            'building_assets',
            'block_building_assets',
            'building_types',
            'block_inspection_values',
            'block_inspection_value_types',
            'block_unit_types',
            'block_types',
            'priorities',
            'issue_statuses',
            'issue_types',
            'job_statuses',
            'job_reasons',
            'model_has_permissions',  // Permission system tables
            'model_has_roles',
            'role_has_permissions',
            'users',  // Clear users before user_types
            'permissions',
            'roles',
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
