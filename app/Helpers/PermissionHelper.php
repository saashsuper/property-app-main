<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if the current user has any of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public static function hasAnyPermission(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (Auth::user()->can($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the current user has all of the given permissions
     *
     * @param array $permissions
     * @return bool
     */
    public static function hasAllPermissions(array $permissions): bool
    {
        if (!Auth::check()) {
            return false;
        }

        foreach ($permissions as $permission) {
            if (!Auth::user()->can($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all permissions for a module
     *
     * @param string $module
     * @return array
     */
    public static function getModulePermissions(string $module): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getAllPermissions()
            ->filter(function ($permission) use ($module) {
                return str_starts_with($permission->name, $module . '.');
            })
            ->pluck('name')
            ->toArray();
    }

    /**
     * Check if user can perform any action on a module
     *
     * @param string $module
     * @return bool
     */
    public static function canAccessModule(string $module): bool
    {
        return !empty(self::getModulePermissions($module));
    }

    /**
     * Get user's role names
     *
     * @return array
     */
    public static function getUserRoles(): array
    {
        if (!Auth::check()) {
            return [];
        }

        return Auth::user()->getRoleNames()->toArray();
    }

    /**
     * Check if user is admin (has Admin or Super Admin role)
     *
     * @return bool
     */
    public static function isAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasAnyRole(['Admin', 'Super Admin']);
    }

    /**
     * Check if user is super admin
     *
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->hasRole('Super Admin');
    }

    /**
     * Get permissions grouped by module
     *
     * @return array
     */
    public static function getGroupedPermissions(): array
    {
        if (!Auth::check()) {
            return [];
        }

        $permissions = Auth::user()->getAllPermissions();
        $grouped = [];

        foreach ($permissions as $permission) {
            $parts = explode('.', $permission->name);
            $module = $parts[0];
            $action = $parts[1] ?? '';

            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }

            $grouped[$module][] = $action;
        }

        return $grouped;
    }
}

