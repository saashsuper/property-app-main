<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // @role directive
        Blade::if('role', function ($role) {
            return Auth::check() && Auth::user()->hasRole($role);
        });

        // @admin directive
        Blade::if('admin', function () {
            return Auth::check() && Auth::user()->isAdmin();
        });

        // @hasAnyRole directive
        Blade::if('hasAnyRole', function ($roles) {
            if (!Auth::check()) {
                return false;
            }
            
            $userRole = Auth::user()->userRole;
            if (!$userRole) {
                return false;
            }
            
            $roleArray = is_array($roles) ? $roles : explode('|', $roles);
            return in_array($userRole->name, $roleArray);
        });
    }
}
