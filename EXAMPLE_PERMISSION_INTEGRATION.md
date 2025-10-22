# Example: Integrating Permissions into Existing Controllers

This guide shows you how to add permission checks to your existing controllers.

---

## Example 1: Block Controller Integration

### Before (No Permissions)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        $blocks = Block::all();
        return view('blocks.index', compact('blocks'));
    }
    
    public function create()
    {
        return view('blocks.create');
    }
    
    public function store(Request $request)
    {
        $block = Block::create($request->validated());
        return redirect()->route('blocks.show', $block);
    }
    
    public function edit(Block $block)
    {
        return view('blocks.edit', compact('block'));
    }
    
    public function update(Request $request, Block $block)
    {
        $block->update($request->validated());
        return redirect()->route('blocks.show', $block);
    }
    
    public function destroy(Block $block)
    {
        $block->delete();
        return redirect()->route('blocks.index');
    }
}
```

### After (With Permissions)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        // Check permission
        if (!auth()->user()->can('blocks.view')) {
            abort(403, 'You do not have permission to view blocks.');
        }
        
        $blocks = Block::all();
        return view('blocks.index', compact('blocks'));
    }
    
    public function create()
    {
        // Check permission
        if (!auth()->user()->can('blocks.create')) {
            return redirect()->route('blocks.index')
                ->with('error', 'You do not have permission to create blocks.');
        }
        
        return view('blocks.create');
    }
    
    public function store(Request $request)
    {
        // Check permission
        if (!auth()->user()->can('blocks.create')) {
            abort(403, 'You do not have permission to create blocks.');
        }
        
        $block = Block::create($request->validated());
        return redirect()->route('blocks.show', $block)
            ->with('success', 'Block created successfully.');
    }
    
    public function edit(Block $block)
    {
        // Check permission
        if (!auth()->user()->can('blocks.edit')) {
            return redirect()->route('blocks.index')
                ->with('error', 'You do not have permission to edit blocks.');
        }
        
        return view('blocks.edit', compact('block'));
    }
    
    public function update(Request $request, Block $block)
    {
        // Check permission
        if (!auth()->user()->can('blocks.edit')) {
            abort(403, 'You do not have permission to edit blocks.');
        }
        
        $block->update($request->validated());
        return redirect()->route('blocks.show', $block)
            ->with('success', 'Block updated successfully.');
    }
    
    public function destroy(Block $block)
    {
        // Check permission - only admins and managers
        if (!auth()->user()->can('blocks.delete')) {
            abort(403, 'You do not have permission to delete blocks.');
        }
        
        $block->delete();
        return redirect()->route('blocks.index')
            ->with('success', 'Block deleted successfully.');
    }
}
```

---

## Example 2: Using Policies (Recommended Approach)

### Create the Policy

```bash
ddev exec php artisan make:policy BlockPolicy --model=Block
```

### Define Policy Methods

```php
<?php

namespace App\Policies;

use App\Models\Block;
use App\Models\User;

class BlockPolicy
{
    /**
     * Determine if user can view any blocks
     */
    public function viewAny(User $user): bool
    {
        return $user->can('blocks.view');
    }
    
    /**
     * Determine if user can view a specific block
     */
    public function view(User $user, Block $block): bool
    {
        return $user->can('blocks.view');
    }
    
    /**
     * Determine if user can create blocks
     */
    public function create(User $user): bool
    {
        return $user->can('blocks.create');
    }
    
    /**
     * Determine if user can update blocks
     */
    public function update(User $user, Block $block): bool
    {
        return $user->can('blocks.edit');
    }
    
    /**
     * Determine if user can delete blocks
     */
    public function delete(User $user, Block $block): bool
    {
        return $user->can('blocks.delete');
    }
    
    /**
     * Determine if user can export blocks
     */
    public function export(User $user): bool
    {
        return $user->can('blocks.export');
    }
}
```

### Register Policy

In `app/Providers/AuthServiceProvider.php`:

```php
<?php

namespace App\Providers;

use App\Models\Block;
use App\Policies\BlockPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Block::class => BlockPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
```

### Use Policy in Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\Block;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Block::class);
        
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
        
        $block = Block::create($request->validated());
        return redirect()->route('blocks.show', $block);
    }
    
    public function edit(Block $block)
    {
        $this->authorize('update', $block);
        
        return view('blocks.edit', compact('block'));
    }
    
    public function update(Request $request, Block $block)
    {
        $this->authorize('update', $block);
        
        $block->update($request->validated());
        return redirect()->route('blocks.show', $block);
    }
    
    public function destroy(Block $block)
    {
        $this->authorize('delete', $block);
        
        $block->delete();
        return redirect()->route('blocks.index');
    }
}
```

---

## Example 3: Route Protection

### Before

```php
Route::middleware(['auth'])->group(function () {
    Route::resource('blocks', BlockController::class);
});
```

### After

```php
Route::middleware(['auth'])->group(function () {
    // View blocks
    Route::get('/blocks', [BlockController::class, 'index'])
        ->middleware('permission:blocks.view');
    
    Route::get('/blocks/{block}', [BlockController::class, 'show'])
        ->middleware('permission:blocks.view');
    
    // Create blocks
    Route::middleware(['permission:blocks.create'])->group(function () {
        Route::get('/blocks/create', [BlockController::class, 'create']);
        Route::post('/blocks', [BlockController::class, 'store']);
    });
    
    // Edit blocks
    Route::middleware(['permission:blocks.edit'])->group(function () {
        Route::get('/blocks/{block}/edit', [BlockController::class, 'edit']);
        Route::put('/blocks/{block}', [BlockController::class, 'update']);
    });
    
    // Delete blocks
    Route::delete('/blocks/{block}', [BlockController::class, 'destroy'])
        ->middleware('permission:blocks.delete');
});
```

Or more concisely:

```php
Route::middleware(['auth', 'permission:blocks.view'])->group(function () {
    Route::get('/blocks', [BlockController::class, 'index']);
    Route::get('/blocks/{block}', [BlockController::class, 'show']);
});

Route::middleware(['auth', 'permission:blocks.create'])->group(function () {
    Route::get('/blocks/create', [BlockController::class, 'create']);
    Route::post('/blocks', [BlockController::class, 'store']);
});

Route::middleware(['auth', 'permission:blocks.edit'])->group(function () {
    Route::get('/blocks/{block}/edit', [BlockController::class, 'edit']);
    Route::put('/blocks/{block}', [BlockController::class, 'update']);
});

Route::middleware(['auth', 'permission:blocks.delete'])->group(function () {
    Route::delete('/blocks/{block}', [BlockController::class, 'destroy']);
});
```

---

## Example 4: View Integration

### blocks/index.blade.php

### Before

```blade
<div class="card">
    <div class="card-header">
        <h4>Blocks</h4>
        <a href="{{ route('blocks.create') }}" class="btn btn-primary">
            Create Block
        </a>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($blocks as $block)
                <tr>
                    <td>{{ $block->name }}</td>
                    <td>{{ $block->location }}</td>
                    <td>
                        <a href="{{ route('blocks.edit', $block) }}">Edit</a>
                        <form action="{{ route('blocks.destroy', $block) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

### After

```blade
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4>Blocks</h4>
        
        @can('blocks.create')
            <a href="{{ route('blocks.create') }}" class="btn btn-primary">
                <i class="ph-plus"></i> Create Block
            </a>
        @endcan
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
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
                    
                    @canany(['blocks.edit', 'blocks.delete'])
                    <td>
                        <div class="btn-group">
                            @can('blocks.view')
                                <a href="{{ route('blocks.show', $block) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="ph-eye"></i>
                                </a>
                            @endcan
                            
                            @can('blocks.edit')
                                <a href="{{ route('blocks.edit', $block) }}" 
                                   class="btn btn-sm btn-warning">
                                    <i class="ph-pencil"></i>
                                </a>
                            @endcan
                            
                            @can('blocks.delete')
                                <form action="{{ route('blocks.destroy', $block) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure?')">
                                        <i class="ph-trash"></i>
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
</div>
```

---

## Example 5: Advanced - Conditional Logic

### Custom Logic with Multiple Permissions

```php
public function approve(Block $block)
{
    // Check if user has approve permission AND is a manager or admin
    if (!auth()->user()->can('blocks.approve') || 
        !auth()->user()->hasAnyRole(['Admin', 'Manager'])) {
        abort(403, 'Only managers and admins can approve blocks.');
    }
    
    // Additional business logic
    if ($block->status === 'approved') {
        return redirect()->back()
            ->with('error', 'Block is already approved.');
    }
    
    $block->update(['status' => 'approved']);
    
    return redirect()->route('blocks.show', $block)
        ->with('success', 'Block approved successfully.');
}
```

### Different Permissions Based on Status

```php
public function edit(Block $block)
{
    // Completed blocks can only be edited by admins
    if ($block->status === 'completed') {
        if (!auth()->user()->hasRole('Admin')) {
            abort(403, 'Only admins can edit completed blocks.');
        }
    } else {
        // Regular edit permission for non-completed blocks
        if (!auth()->user()->can('blocks.edit')) {
            abort(403, 'You do not have permission to edit blocks.');
        }
    }
    
    return view('blocks.edit', compact('block'));
}
```

---

## Example 6: API Endpoints with Permissions

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Block;
use Illuminate\Http\Request;

class BlockApiController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('blocks.view')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have permission to view blocks.'
            ], 403);
        }
        
        $blocks = Block::all();
        return response()->json($blocks);
    }
    
    public function store(Request $request)
    {
        if (!auth()->user()->can('blocks.create')) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'You do not have permission to create blocks.'
            ], 403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string',
        ]);
        
        $block = Block::create($validated);
        
        return response()->json($block, 201);
    }
}
```

---

## Quick Reference

### In Controllers
```php
// Simple check
if (!auth()->user()->can('permission.name')) {
    abort(403);
}

// With policy
$this->authorize('action', Model::class);

// Check role
if (!auth()->user()->hasRole('Admin')) {
    abort(403);
}
```

### In Routes
```php
// Single permission
Route::middleware(['permission:blocks.view']);

// Multiple (OR)
Route::middleware(['permission:blocks.edit|blocks.delete']);

// Role
Route::middleware(['role:Admin']);
```

### In Views
```blade
@can('permission.name')
    <!-- content -->
@endcan

@cannot('permission.name')
    <!-- content -->
@endcannot

@canany(['perm1', 'perm2'])
    <!-- content -->
@endcanany

@role('Admin')
    <!-- content -->
@endrole
```

---

## Migration Checklist

When adding permissions to existing features:

- [ ] Add permissions to `PermissionsSeeder.php`
- [ ] Run seeder: `ddev exec php artisan db:seed --class=PermissionsSeeder`
- [ ] Create policy (optional but recommended)
- [ ] Add checks to controller methods
- [ ] Protect routes with middleware
- [ ] Update views with `@can` directives
- [ ] Test with different roles
- [ ] Update documentation

---

## Testing Different Roles

```bash
# In tinker
ddev exec php artisan tinker

# Test as different users
$admin = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first();
$manager = User::whereHas('roles', fn($q) => $q->where('name', 'Manager'))->first();
$viewer = User::whereHas('roles', fn($q) => $q->where('name', 'Viewer'))->first();

# Check permissions
$admin->can('blocks.delete');    // true
$manager->can('blocks.delete');  // false
$viewer->can('blocks.delete');   // false

# List all permissions for a user
$user->getAllPermissions()->pluck('name');
```

---

This example demonstrates how to systematically add permission checks to your existing codebase. Start with high-priority features and gradually expand to cover all functionality.

