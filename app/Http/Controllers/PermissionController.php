<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index()
    {
        if (!auth()->user()->can('permissions.view')) {
            abort(403, 'Unauthorized access.');
        }
        
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create()
    {
        if (!auth()->user()->can('permissions.create')) {
            abort(403, 'Unauthorized access.');
        }
        
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('permissions.create')) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        try {
            Permission::create(['name' => $request->name]);
            
            return redirect()->route('permissions.index')
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error creating permission: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(Permission $permission)
    {
        if (!auth()->user()->can('permissions.view')) {
            abort(403, 'Unauthorized access.');
        }
        
        $permission->load('roles');
        
        return view('permissions.show', compact('permission'));
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(Permission $permission)
    {
        if (!auth()->user()->can('permissions.delete')) {
            abort(403, 'Unauthorized access.');
        }
        
        try {
            $permission->delete();
            
            return redirect()->route('permissions.index')
                ->with('success', 'Permission deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting permission: ' . $e->getMessage());
        }
    }

    /**
     * Sync permissions to a role
     */
    public function syncToRole(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role->syncPermissions($request->permissions);
            
            return response()->json([
                'success' => true,
                'message' => 'Permissions synced successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error syncing permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}

