<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class DatabaseServiceProvider extends ServiceProvider
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
        // Only apply this logic in local environment
        if ($this->app->environment('local')) {
            $this->configureDatabaseForEnvironment();
        }
    }

    /**
     * Configure database connection based on environment
     */
    private function configureDatabaseForEnvironment(): void
    {
        // Check if we're running in console (CLI) and in DDEV environment
        if ($this->app->runningInConsole() && $this->isDdevEnvironment()) {
            // Use DDEV internal connection for CLI commands inside container
            Config::set('database.connections.mysql.host', 'db');
            Config::set('database.connections.mysql.port', '3306');
        }
        // For web requests and host machine commands, use the .env settings (external connection)
    }

    /**
     * Check if we're running in DDEV environment
     */
    private function isDdevEnvironment(): bool
    {
        // Check if we're inside a DDEV container
        return file_exists('/var/www/html/.ddev/config.yaml') || 
               getenv('DDEV_SITENAME') !== false ||
               getenv('DDEV_PROJECT') !== false ||
               getenv('DDEV_DOCROOT') !== false;
    }
} 