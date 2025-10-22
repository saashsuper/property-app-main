# Quick Start Guide - Permission System

## 🚀 Getting Started in 5 Minutes

### 1. Login as Super Admin
- Email: `admin@proman.com`
- This account has full system access

### 2. Access Permission Management
Navigate to `/roles` to view and manage roles and permissions.

### 3. Assign Roles to Users
1. Go to **Users** → **Edit User**
2. Scroll to "Assign Roles" section
3. Check the desired roles
4. Save

---

## 📋 Common Tasks

### Protect a Controller Method

```php
public function index()
{
    // Method 1: Simple check
    if (!auth()->user()->can('blocks.view')) {
        abort(403);
    }
    
    // Method 2: Using authorize
    $this->authorize('viewAny', Block::class);
    
    // Your logic here
}
```

### Protect a Route

```php
// In routes/web.php
Route::middleware(['permission:blocks.edit'])->group(function () {
    Route::get('/blocks/{block}/edit', [BlockController::class, 'edit']);
});
```

### Show/Hide Buttons in Views

```blade
@can('blocks.create')
    <a href="{{ route('blocks.create') }}" class="btn btn-primary">
        Create Block
    </a>
@endcan

@can('blocks.edit')
    <button class="btn btn-warning">Edit</button>
@endcan

@can('blocks.delete')
    <button class="btn btn-danger">Delete</button>
@endcan
```

---

## 🎯 Permission Naming

All permissions follow this pattern: `{module}.{action}`

**Examples:**
- `blocks.view` - View blocks
- `blocks.create` - Create new blocks
- `blocks.edit` - Edit existing blocks
- `blocks.delete` - Delete blocks
- `work-orders.approve` - Approve work orders
- `issues.assign` - Assign issues to users

---

## 👥 Default Roles

### Super Admin
- ✅ All permissions (96 permissions)
- Use for: System administrators

### Admin
- ✅ All permissions (96 permissions)
- Use for: Application administrators

### Manager
- ✅ Management permissions
- ✅ Can approve work orders and inspections
- Use for: Property managers, supervisors

### Inspector
- ✅ Inspection and issue creation
- ✅ Can create visits and inspections
- Use for: Field inspectors

### Contractor
- ✅ View assigned work
- ✅ Update work orders
- Use for: Contractors

### Viewer
- ✅ Read-only access
- Use for: Stakeholders, reporting users

---

## 🔧 Quick Commands

```bash
# Clear permission cache
ddev exec php artisan permission:cache-reset

# Re-run permissions seeder
ddev exec php artisan db:seed --class=PermissionsSeeder

# Assign Super Admin role to a user (in tinker)
ddev exec php artisan tinker
>>> $user = User::find(1);
>>> $user->assignRole('Super Admin');
```

---

## 📚 Full Documentation

For detailed documentation, see `PERMISSION_SYSTEM_DOCUMENTATION.md`

---

## ✅ Implementation Checklist

When adding permissions to a new feature:

- [ ] Define permissions in `PermissionsSeeder.php`
- [ ] Re-run seeder: `ddev exec php artisan db:seed --class=PermissionsSeeder`
- [ ] Protect controller methods
- [ ] Add middleware to routes
- [ ] Use `@can` directives in views
- [ ] Create policy if needed
- [ ] Test with different roles

---

## 🆘 Common Issues

### "Permission not found"
```bash
# Clear cache
ddev exec php artisan permission:cache-reset
```

### "User doesn't have permission"
1. Check if user has the correct role
2. Check if role has the permission
3. Clear cache

### Adding New Permissions
1. Edit `database/seeders/PermissionsSeeder.php`
2. Add your permission to the `$modules` array
3. Run: `ddev exec php artisan db:seed --class=PermissionsSeeder`
4. Assign to roles as needed

