# Permission System Documentation

## Overview

This application now has a comprehensive **Role-Based Access Control (RBAC)** system using **Spatie Laravel Permission** package. This allows you to control what users can do based on their assigned roles and permissions.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Database Structure](#database-structure)
3. [Roles & Permissions](#roles--permissions)
4. [Usage in Controllers](#usage-in-controllers)
5. [Usage in Blade Views](#usage-in-blade-views)
6. [Usage in Routes](#usage-in-routes)
7. [Managing Roles & Permissions](#managing-roles--permissions)
8. [Permission Naming Convention](#permission-naming-convention)
9. [API Reference](#api-reference)
10. [Examples](#examples)

---

## Quick Start

### Installation Complete ✅

The following has been set up for you:

- ✅ Spatie Laravel Permission package installed
- ✅ Database migrations created and run
- ✅ Roles and permissions seeded
- ✅ User model configured with HasRoles trait
- ✅ Middleware registered
- ✅ Controllers and views created
- ✅ Routes configured

### Initial Setup

**Super Admin** role has been assigned to: `admin@proman.com`

### Available Roles

1. **Super Admin** - Full system access (all permissions)
2. **Admin** - Administrative access (most permissions except system settings)
3. **Manager** - Management access (can manage blocks, issues, approve work orders)
4. **Inspector** - Inspection focused access (can create inspections, issues, visits)
5. **Contractor** - Limited access (can view and update assigned work)
6. **Viewer** - Read-only access

---

## Database Structure

### Tables Created

1. **roles** - Stores all roles
2. **permissions** - Stores all permissions
3. **model_has_permissions** - Direct permissions to users (optional)
4. **model_has_roles** - User role assignments
5. **role_has_permissions** - Permission assignments to roles

### Relationships

```
User
 ├── roles (many-to-many)
 └── permissions (many-to-many, direct)

Role
 ├── users (many-to-many)
 └── permissions (many-to-many)
```

---

## Roles & Permissions

### Permission Structure

Permissions follow the pattern: `{module}.{action}`

**Example:**
- `blocks.view` - View blocks
- `blocks.create` - Create blocks
- `blocks.edit` - Edit blocks
- `blocks.delete` - Delete blocks

### Modules and Actions

#### Blocks Module
- `blocks.view`, `blocks.create`, `blocks.edit`, `blocks.delete`, `blocks.export`
- `block-buildings.view`, `block-buildings.create`, `block-buildings.edit`, `block-buildings.delete`
- `block-units.view`, `block-units.create`, `block-units.edit`, `block-units.delete`
- `block-contractors.view`, `block-contractors.create`, `block-contractors.edit`, `block-contractors.delete`
- `block-information.view`, `block-information.create`, `block-information.edit`, `block-information.delete`
- `block-images.view`, `block-images.upload`, `block-images.delete`

#### Issues Module
- `block-issues.view`, `block-issues.create`, `block-issues.edit`, `block-issues.delete`, `block-issues.assign`, `block-issues.resolve`
- `block-issue-actions.view`, `block-issue-actions.create`, `block-issue-actions.edit`, `block-issue-actions.delete`
- `issues.view`, `issues.create`, `issues.edit`, `issues.delete`, `issues.assign`
- `issue-logs.view`, `issue-logs.create`

#### Inspections Module
- `block-inspections.view`, `block-inspections.create`, `block-inspections.edit`, `block-inspections.delete`, `block-inspections.approve`
- `block-inspection-teams.view`, `block-inspection-teams.create`, `block-inspection-teams.edit`, `block-inspection-teams.delete`
- `block-inspection-assets.view`, `block-inspection-assets.create`, `block-inspection-assets.edit`, `block-inspection-assets.delete`

#### Work Orders Module
- `work-orders.view`, `work-orders.create`, `work-orders.edit`, `work-orders.delete`, `work-orders.approve`, `work-orders.complete`
- `block-work-orders.view`, `block-work-orders.create`, `block-work-orders.edit`, `block-work-orders.delete`

#### Visits Module
- `block-visits.view`, `block-visits.create`, `block-visits.edit`, `block-visits.delete`
- `block-visit-results.view`, `block-visit-results.create`, `block-visit-results.edit`, `block-visit-results.delete`

#### User Management Module
- `users.view`, `users.create`, `users.edit`, `users.delete`, `users.restore`
- `user-types.view`, `user-types.create`, `user-types.edit`, `user-types.delete`

#### Roles & Permissions Module
- `roles.view`, `roles.create`, `roles.edit`, `roles.delete`, `roles.assign-permissions`
- `permissions.view`, `permissions.assign`

#### System Module
- `reports.view`, `reports.export`
- `dashboard.view`, `dashboard.view-analytics`
- `settings.view`, `settings.edit`
- `system-settings.view`, `system-settings.edit`

---

## Usage in Controllers

### Method 1: Using Middleware

```php
// In your routes/web.php
Route::middleware(['permission:blocks.view'])->group(function () {
    Route::get('/blocks', [BlockController::class, 'index']);
});

Route::middleware(['permission:blocks.edit'])->group(function () {
    Route::put('/blocks/{block}', [BlockController::class, 'update']);
});

// Multiple permissions (any)
Route::middleware(['permission:blocks.edit|blocks.delete'])->group(function () {
    // User needs either edit OR delete permission
});
```

### Method 2: Using authorize() in Controllers

```php
class BlockController extends Controller
{
    public function index()
    {
        // Check permission, throws 403 if unauthorized
        $this->authorize('viewAny', Block::class);
        
        $blocks = Block::all();
        return view('blocks.index', compact('blocks'));
    }
    
    public function edit(Block $block)
    {
        // Using policy
        $this->authorize('update', $block);
        
        return view('blocks.edit', compact('block'));
    }
    
    public function destroy(Block $block)
    {
        // Direct permission check
        if (!auth()->user()->can('blocks.delete')) {
            abort(403, 'Unauthorized action.');
        }
        
        $block->delete();
        return redirect()->route('blocks.index');
    }
}
```

### Method 3: Manual Checks

```php
public function someMethod()
{
    // Check if user has permission
    if (auth()->user()->can('blocks.create')) {
        // User has permission
    }
    
    // Check if user has role
    if (auth()->user()->hasRole('Admin')) {
        // User is admin
    }
    
    // Check if user has any role
    if (auth()->user()->hasAnyRole(['Admin', 'Manager'])) {
        // User is either admin or manager
    }
    
    // Check if user has all roles
    if (auth()->user()->hasAllRoles(['Admin', 'Manager'])) {
        // User has both roles
    }
}
```

---

## Usage in Blade Views

### Check Permissions

```blade
@can('blocks.create')
    <a href="{{ route('blocks.create') }}" class="btn btn-primary">
        Create New Block
    </a>
@endcan

@can('blocks.edit')
    <a href="{{ route('blocks.edit', $block->id) }}" class="btn btn-warning">
        Edit
    </a>
@endcan

@can('blocks.delete')
    <form action="{{ route('blocks.destroy', $block->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
@endcan
```

### Check Multiple Permissions

```blade
{{-- User needs ANY of these permissions --}}
@canany(['blocks.edit', 'blocks.delete'])
    <div class="action-buttons">
        @can('blocks.edit')
            <button>Edit</button>
        @endcan
        
        @can('blocks.delete')
            <button>Delete</button>
        @endcan
    </div>
@endcanany

{{-- Opposite --}}
@cannot('blocks.delete')
    <p>You cannot delete this block</p>
@endcannot
```

### Check Roles

```blade
@role('Admin')
    <div class="admin-panel">
        <!-- Admin-only content -->
    </div>
@endrole

@hasrole('Manager')
    <p>Welcome, Manager!</p>
@endhasrole

@hasanyrole('Admin|Manager')
    <p>You have management access</p>
@endhasanyrole

@unlessrole('Admin')
    <p>You are not an admin</p>
@endunlessrole
```

### Combined Checks

```blade
@role('Admin')
    @can('blocks.delete')
        <!-- Only admins with delete permission see this -->
        <button class="btn btn-danger">Delete All</button>
    @endcan
@endrole
```

---

## Usage in Routes

### Protecting Routes with Middleware

```php
// Single permission
Route::middleware(['permission:blocks.view'])->group(function () {
    Route::get('/blocks', [BlockController::class, 'index']);
});

// Multiple permissions (OR)
Route::middleware(['permission:blocks.edit|blocks.delete'])->group(function () {
    Route::resource('blocks', BlockController::class);
});

// Role-based
Route::middleware(['role:Admin'])->group(function () {
    Route::resource('users', UserController::class);
});

// Multiple roles (OR)
Route::middleware(['role:Admin|Manager'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
});

// Role OR Permission
Route::middleware(['role_or_permission:Admin|blocks.view'])->group(function () {
    Route::get('/blocks', [BlockController::class, 'index']);
});
```

---

## Managing Roles & Permissions

### Web Interface

1. **View Roles**: Navigate to `/roles`
2. **Create Role**: Click "Create New Role" button
3. **Edit Role**: Click edit icon on any role
4. **Assign Permissions**: Check/uncheck permissions when creating/editing roles
5. **Delete Role**: Click delete icon (cannot delete roles with users)

### View Permissions

Navigate to `/permissions` to see all available permissions grouped by module.

### Assign Roles to Users

When creating or editing users:
1. Go to `/users/create` or `/users/{id}/edit`
2. Scroll to "Assign Roles" section
3. Check the roles you want to assign
4. Save the user

### Programmatically

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

// Create a role
$role = Role::create(['name' => 'Manager']);

// Create a permission
$permission = Permission::create(['name' => 'blocks.approve']);

// Assign permission to role
$role->givePermissionTo('blocks.view');
$role->givePermissionTo(['blocks.create', 'blocks.edit']);

// Remove permission from role
$role->revokePermissionTo('blocks.delete');

// Sync permissions (replace all)
$role->syncPermissions(['blocks.view', 'blocks.create', 'blocks.edit']);

// Assign role to user
$user = User::find(1);
$user->assignRole('Manager');
$user->assignRole(['Manager', 'Inspector']);

// Remove role from user
$user->removeRole('Manager');

// Sync roles (replace all)
$user->syncRoles(['Manager']);

// Give direct permission to user (bypass role)
$user->givePermissionTo('blocks.delete');
```

---

## Permission Naming Convention

### Pattern
```
{module}.{action}
```

### Actions
- **view** - List/read access
- **create** - Create new records
- **edit** - Update existing records
- **delete** - Delete records
- **approve** - Approve/authorize records
- **assign** - Assign to users
- **export** - Export data
- **upload** - Upload files
- **restore** - Restore soft-deleted records

### Examples
```
blocks.view
blocks.create
blocks.edit
blocks.delete
blocks.export

work-orders.approve
work-orders.complete

issues.assign
issues.resolve
```

---

## API Reference

### User Methods

```php
// Check permissions
$user->can('blocks.edit');
$user->cannot('blocks.delete');
$user->hasPermissionTo('blocks.view');
$user->hasAnyPermission(['blocks.edit', 'blocks.delete']);
$user->hasAllPermissions(['blocks.view', 'blocks.edit']);

// Check roles
$user->hasRole('Admin');
$user->hasAnyRole(['Admin', 'Manager']);
$user->hasAllRoles(['Admin', 'Manager']);

// Get permissions
$user->permissions; // Collection of Permission models
$user->getAllPermissions(); // Collection including role permissions
$user->getPermissionNames(); // Collection of permission names

// Get roles
$user->roles; // Collection of Role models
$user->getRoleNames(); // Collection of role names

// Assign/Remove permissions
$user->givePermissionTo('blocks.edit');
$user->revokePermissionTo('blocks.edit');
$user->syncPermissions(['blocks.view', 'blocks.edit']);

// Assign/Remove roles
$user->assignRole('Manager');
$user->removeRole('Manager');
$user->syncRoles(['Manager', 'Inspector']);
```

### Role Methods

```php
$role = Role::findByName('Admin');

// Get permissions
$role->permissions; // Collection of Permission models
$role->getPermissionNames(); // Collection of permission names

// Assign/Remove permissions
$role->givePermissionTo('blocks.edit');
$role->revokePermissionTo('blocks.edit');
$role->syncPermissions(['blocks.view', 'blocks.edit']);

// Get users
$role->users; // Collection of User models
```

---

## Examples

### Example 1: Protect Block Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        // Anyone authenticated can view
        if (!auth()->user()->can('blocks.view')) {
            abort(403, 'Unauthorized to view blocks');
        }
        
        $blocks = Block::all();
        return view('blocks.index', compact('blocks'));
    }
    
    public function create()
    {
        $this->authorize('create', Block::class);
        return view('blocks.create');
    }
    
    public function store(Request $request)
    {
        $this->authorize('create', Block::class);
        
        // Validation and creation logic
        $block = Block::create($request->validated());
        
        return redirect()->route('blocks.show', $block);
    }
    
    public function edit(Block $block)
    {
        if (!auth()->user()->can('blocks.edit')) {
            return redirect()->back()->with('error', 'You do not have permission to edit blocks');
        }
        
        return view('blocks.edit', compact('block'));
    }
    
    public function destroy(Block $block)
    {
        // Only admins and managers can delete
        if (!auth()->user()->hasAnyRole(['Admin', 'Manager'])) {
            abort(403, 'Only admins and managers can delete blocks');
        }
        
        $block->delete();
        return redirect()->route('blocks.index')->with('success', 'Block deleted');
    }
}
```

### Example 2: Block Index View with Permissions

```blade
@extends('layouts.master')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Blocks</h1>
        
        @can('blocks.create')
            <a href="{{ route('blocks.create') }}" class="btn btn-primary">
                <i class="ph-plus"></i> Create Block
            </a>
        @endcan
    </div>
    
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Location</th>
                <th>Status</th>
                @canany(['blocks.edit', 'blocks.delete'])
                    <th>Actions</th>
                @endcanany
            </tr>
        </thead>
        <tbody>
            @foreach($blocks as $block)
            <tr>
                <td>{{ $block->name }}</td>
                <td>{{ $block->location }}</td>
                <td>{{ $block->status }}</td>
                
                @canany(['blocks.edit', 'blocks.delete'])
                <td>
                    <div class="btn-group">
                        @can('blocks.view')
                            <a href="{{ route('blocks.show', $block) }}" class="btn btn-sm btn-info">
                                View
                            </a>
                        @endcan
                        
                        @can('blocks.edit')
                            <a href="{{ route('blocks.edit', $block) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>
                        @endcan
                        
                        @can('blocks.delete')
                            <form action="{{ route('blocks.destroy', $block) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </div>
                </td>
                @endcanany
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
```

### Example 3: Create Policy for Blocks

```php
<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('blocks.view');
    }
    
    public function view(User $user, Block $block): bool
    {
        return $user->can('blocks.view');
    }
    
    public function create(User $user): bool
    {
        return $user->can('blocks.create');
    }
    
    public function update(User $user, Block $block): bool
    {
        return $user->can('blocks.edit');
    }
    
    public function delete(User $user, Block $block): bool
    {
        // Only admins can delete, or managers if block is not completed
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        if ($user->hasRole('Manager') && $block->status !== 'completed') {
            return $user->can('blocks.delete');
        }
        
        return false;
    }
}
```

Register the policy in `AuthServiceProvider.php`:

```php
protected $policies = [
    Block::class => BlockPolicy::class,
];
```

---

## Best Practices

### 1. Use Policies When Possible
Policies provide a clean, organized way to authorize actions.

### 2. Check Permissions, Not Roles
```php
// ❌ Bad
if ($user->hasRole('Admin')) {
    // do something
}

// ✅ Good
if ($user->can('blocks.delete')) {
    // do something
}
```

### 3. Group Related Routes
```php
Route::middleware(['permission:blocks.view'])->group(function () {
    Route::get('/blocks', [BlockController::class, 'index']);
    Route::get('/blocks/{block}', [BlockController::class, 'show']);
});
```

### 4. Use Meaningful Permission Names
Follow the `{module}.{action}` pattern consistently.

### 5. Cache Permissions
Spatie caches permissions automatically. Clear cache when updating:
```bash
php artisan permission:cache-reset
```

### 6. Don't Hardcode Super Admin
Super Admin should have all permissions via the database, not hardcoded checks.

---

## Troubleshooting

### Permission not working?
```bash
# Clear permission cache
ddev exec php artisan permission:cache-reset

# Clear application cache
ddev exec php artisan cache:clear
```

### User doesn't have expected permissions?
Check:
1. User has the correct role assigned
2. Role has the correct permissions
3. Permission name is spelled correctly
4. Cache has been cleared

### Check user permissions:
```php
$user = User::find(1);
dd($user->getAllPermissions());
dd($user->getRoleNames());
```

---

## Security Notes

1. **Always validate on the server-side** - Never rely only on hiding UI elements
2. **Use middleware** - Protect routes at the middleware level
3. **Test permissions** - Create automated tests for critical permissions
4. **Audit trail** - Consider logging permission-based actions
5. **Principle of least privilege** - Give users minimum permissions needed

---

## Additional Resources

- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission)
- [Laravel Authorization](https://laravel.com/docs/authorization)
- [Laravel Policies](https://laravel.com/docs/authorization#creating-policies)

---

## Support

For issues or questions, contact your system administrator or refer to the Spatie Laravel Permission documentation.

**Version:** 1.0  
**Last Updated:** October 19, 2025

