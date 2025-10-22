<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index()
    {
        $this->authorize('viewAny', Role::class);
        
        $roles = Role::with('permissions')->paginate(15);
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorize('create', Role::class);
        
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role = Role::create(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorize('view', $role);
        
        $role->load('permissions', 'users');
        
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorize('update', $role);
        
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('update', $role);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $role->update(['name' => $request->name]);
            
            $role->syncPermissions($request->permissions ?? []);
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete', $role);
        
        // Prevent deletion of Super Admin role
        if ($role->name === 'Super Admin') {
            return back()->with('error', 'Cannot delete Super Admin role.');
        }
        
        DB::beginTransaction();
        try {
            // Check if role has users
            if ($role->users()->count() > 0) {
                return back()->with('error', 'Cannot delete role with assigned users.');
            }
            
            $role->delete();
            
            DB::commit();
            
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting role: ' . $e->getMessage());
        }
    }

    /**
     * Assign role to user
     */
    public function assignToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);
        
        $user->assignRole($role);
        
        return response()->json([
            'success' => true,
            'message' => 'Role assigned successfully.'
        ]);
    }

    /**
     * Remove role from user
     */
    public function removeFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);
        
        $user->removeRole($role);
        
        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully.'
        ]);
    }
}

