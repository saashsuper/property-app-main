# DDEV Database Connection Setup

This project is configured to work with DDEV for local development. The database connection settings are **automatically managed** to work both from your host machine and inside the DDEV container.

## Quick Start

### 1. Start DDEV
```bash
ddev start
```

### 2. Run Migrations and Seeders
```bash
# Inside DDEV container (recommended)
ddev migrate-fresh-seed

# Or step by step:
ddev migrate-fresh
ddev seed
```

## Database Connection Details

### Automatic Configuration
The system automatically detects the environment and uses the appropriate database connection:

**Inside DDEV Container (CLI commands)**:
- **Host**: `db` (internal container hostname)
- **Port**: `3306` (internal port)
- **Database**: `db`
- **Username**: `db`
- **Password**: `db`

**From Host Machine (Web requests & CLI commands)**:
- **Host**: `127.0.0.1` (localhost)
- **Port**: `32833` (DDEV's exposed port)
- **Database**: `db`
- **Username**: `db`
- **Password**: `db`

## Available Commands

### Database Operations
```bash
# Run migrations
ddev migrate

# Run seeders
ddev seed

# Fresh migrations
ddev migrate-fresh

# Fresh migrations + seeders
ddev migrate-fresh-seed
```

### Standard Laravel Commands
```bash
# Run any Laravel command inside DDEV container
ddev artisan [command]

# Examples:
ddev artisan route:list
ddev artisan tinker
ddev artisan make:controller ExampleController

# Or use exec for more complex commands
ddev exec php artisan [command]
```

### Access DDEV Container
```bash
# SSH into the container
ddev ssh
```

## Environment Files

The project includes multiple environment files:

- `.env` - Main environment file (external connection for web/host)
- `.env.ddev-internal` - Internal DDEV connection (db:3306)
- `.env.local` - Local environment backup
- `.env.ddev` - DDEV environment backup

## Troubleshooting

### Migration Issues
If you encounter migration errors:

1. **Check current environment**:
   ```bash
   ddev describe
   ```

2. **Reset and run fresh**:
   ```bash
   ddev migrate-fresh-seed
   ```

3. **Check database connection**:
   ```bash
   ddev exec php artisan tinker
   # Then run: DB::connection()->getPdo();
   ```

### Connection Refused Errors
- Make sure DDEV is running: `ddev start`
- Check if the correct environment is active
- Verify the database service is healthy: `ddev describe`

### Port Conflicts
If you get port conflicts:
```bash
ddev stop
ddev start
```

## Best Practices

1. **Use DDEV commands** for database operations: `ddev migrate`, `ddev seed`
2. **Use `ddev artisan`** for running Laravel commands inside the container
3. **No manual environment switching** - everything is automatic
4. **Keep DDEV running** during development to avoid connection issues

## Database Access

### Via DDEV
```bash
ddev mysql
```

### Via External Tool
- **Host**: `127.0.0.1`
- **Port**: `32833`
- **Database**: `db`
- **Username**: `db`
- **Password**: `db`

### Via Web Interface
- **phpMyAdmin**: `https://proman.ddev.site:8036`
- **Mailpit**: `https://proman.ddev.site:8026` 