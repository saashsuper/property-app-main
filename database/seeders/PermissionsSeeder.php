<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define modules and their permissions
        $modules = [
            'blocks' => ['view', 'create', 'edit', 'delete', 'export'],
            'block-buildings' => ['view', 'create', 'edit', 'delete'],
            'block-units' => ['view', 'create', 'edit', 'delete'],
            'block-contractors' => ['view', 'create', 'edit', 'delete'],
            'block-information' => ['view', 'create', 'edit', 'delete'],
            'block-images' => ['view', 'upload', 'delete'],
            
            'block-issues' => ['view', 'create', 'edit', 'delete', 'assign', 'resolve'],
            'block-issue-actions' => ['view', 'create', 'edit', 'delete'],
            
            'block-inspections' => ['view', 'create', 'edit', 'delete', 'approve'],
            'block-inspection-teams' => ['view', 'create', 'edit', 'delete'],
            'block-inspection-assets' => ['view', 'create', 'edit', 'delete'],
            
            'block-visits' => ['view', 'create', 'edit', 'delete'],
            'block-visit-results' => ['view', 'create', 'edit', 'delete'],
            
            'work-orders' => ['view', 'create', 'edit', 'delete', 'approve', 'complete'],
            'block-work-orders' => ['view', 'create', 'edit', 'delete'],
            
            'issues' => ['view', 'create', 'edit', 'delete', 'assign'],
            'issue-logs' => ['view', 'create'],
            
            'users' => ['view', 'create', 'edit', 'delete', 'restore'],
            'user-types' => ['view', 'create', 'edit', 'delete'],
            
            'roles' => ['view', 'create', 'edit', 'delete', 'assign-permissions'],
            'permissions' => ['view', 'assign'],
            
            'reports' => ['view', 'export'],
            'dashboard' => ['view', 'view-analytics'],
            
            'settings' => ['view', 'edit'],
            'system-settings' => ['view', 'edit'],
        ];

        // Create permissions (or get existing)
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web'
                ]);
            }
        }

        // Create roles (or get existing)
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $inspector = Role::firstOrCreate(['name' => 'Inspector']);
        $contractor = Role::firstOrCreate(['name' => 'Contractor']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // Super Admin and Admin get all permissions
        $superAdmin->syncPermissions(Permission::all());
        $admin->syncPermissions(Permission::all());

        // Manager permissions
        $managerPermissions = [
            // Blocks - all except delete
            'blocks.view', 'blocks.create', 'blocks.edit', 'blocks.export',
            'block-buildings.view', 'block-buildings.create', 'block-buildings.edit',
            'block-units.view', 'block-units.create', 'block-units.edit',
            'block-contractors.view', 'block-contractors.create', 'block-contractors.edit',
            'block-information.view', 'block-information.create', 'block-information.edit',
            'block-images.view', 'block-images.upload', 'block-images.delete',
            
            // Issues - full access
            'block-issues.view', 'block-issues.create', 'block-issues.edit', 'block-issues.assign', 'block-issues.resolve',
            'block-issue-actions.view', 'block-issue-actions.create', 'block-issue-actions.edit',
            'issues.view', 'issues.create', 'issues.edit', 'issues.assign',
            'issue-logs.view', 'issue-logs.create',
            
            // Inspections - approve access
            'block-inspections.view', 'block-inspections.create', 'block-inspections.edit', 'block-inspections.approve',
            'block-inspection-teams.view', 'block-inspection-teams.create', 'block-inspection-teams.edit',
            'block-inspection-assets.view', 'block-inspection-assets.create', 'block-inspection-assets.edit',
            
            // Visits
            'block-visits.view', 'block-visits.create', 'block-visits.edit',
            'block-visit-results.view', 'block-visit-results.create', 'block-visit-results.edit',
            
            // Work Orders - approve access
            'work-orders.view', 'work-orders.create', 'work-orders.edit', 'work-orders.approve', 'work-orders.complete',
            'block-work-orders.view', 'block-work-orders.create', 'block-work-orders.edit',
            
            // Users - view and create only
            'users.view', 'users.create', 'users.edit',
            
            // Reports and Dashboard
            'reports.view', 'reports.export',
            'dashboard.view', 'dashboard.view-analytics',
            
            // Settings - view only
            'settings.view',
        ];
        $manager->syncPermissions($managerPermissions);

        // Inspector permissions
        $inspectorPermissions = [
            'blocks.view',
            'block-buildings.view',
            'block-units.view',
            'block-information.view',
            'block-images.view', 'block-images.upload',
            
            'block-issues.view', 'block-issues.create', 'block-issues.edit',
            'block-issue-actions.view', 'block-issue-actions.create',
            
            'block-inspections.view', 'block-inspections.create', 'block-inspections.edit',
            'block-inspection-teams.view',
            'block-inspection-assets.view', 'block-inspection-assets.create', 'block-inspection-assets.edit',
            
            'block-visits.view', 'block-visits.create', 'block-visits.edit',
            'block-visit-results.view', 'block-visit-results.create', 'block-visit-results.edit',
            
            'issues.view', 'issues.create',
            'issue-logs.view', 'issue-logs.create',
            
            'dashboard.view',
        ];
        $inspector->syncPermissions($inspectorPermissions);

        // Contractor permissions
        $contractorPermissions = [
            'blocks.view',
            'block-buildings.view',
            'block-units.view',
            'block-contractors.view',
            'block-information.view',
            'block-images.view', 'block-images.upload',
            
            'block-issues.view', 'block-issues.edit',
            'block-issue-actions.view', 'block-issue-actions.create',
            
            'work-orders.view', 'work-orders.edit', 'work-orders.complete',
            'block-work-orders.view', 'block-work-orders.edit',
            
            'issues.view',
            'issue-logs.view',
            
            'dashboard.view',
        ];
        $contractor->syncPermissions($contractorPermissions);

        // Viewer permissions
        $viewerPermissions = [
            'blocks.view',
            'block-buildings.view',
            'block-units.view',
            'block-contractors.view',
            'block-information.view',
            'block-images.view',
            
            'block-issues.view',
            'block-inspections.view',
            'block-visits.view',
            
            'work-orders.view',
            'block-work-orders.view',
            
            'issues.view',
            'issue-logs.view',
            
            'dashboard.view',
            'reports.view',
        ];
        $viewer->syncPermissions($viewerPermissions);

        $this->command->info('Permissions and roles created successfully!');
    }
}

