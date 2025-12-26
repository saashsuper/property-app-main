# Timezone Database Corruption Fix

## Problem
You're encountering a fatal error:
```
Fatal error: Uncaught DateError: Timezone database is corrupt. Please file a bug report as this should never happen
```

This is a PHP/Carbon timezone database corruption issue in the DDEV container.

## Solution

### Step 1: Add Timezone to DDEV Config
I've already added `timezone: UTC` to your `.ddev/config.yaml` file. This ensures the container uses a valid timezone.

### Step 2: Rebuild DDEV Container

Since the timezone database is corrupted, you need to rebuild the container:

```bash
# Stop DDEV
ddev stop

# Remove the web container to force rebuild
ddev poweroff
# Or if that doesn't work:
docker rm ddev-proman-web

# Start DDEV again (this will rebuild the container with fresh timezone data)
ddev start
```

### Alternative: Quick Fix Without Rebuild

If you want to try fixing it without a full rebuild:

```bash
# SSH into the container
ddev ssh

# Inside the container, reinstall tzdata
sudo apt-get update
sudo apt-get install --reinstall tzdata

# Exit container
exit

# Restart DDEV
ddev restart
```

### Step 3: Verify Timezone

After rebuilding, verify the timezone is working:

```bash
ddev exec "php -r 'echo date_default_timezone_get() . PHP_EOL;'"
ddev exec "php -r 'echo (new \DateTime())->format(\"Y-m-d H:i:s\") . PHP_EOL;'"
```

## Why This Happened

This error typically occurs when:
1. The container's timezone database file (`/usr/share/zoneinfo/`) gets corrupted
2. There's a mismatch between the system timezone and PHP's timezone configuration
3. The container image has a bad timezone database

Adding `timezone: UTC` to the DDEV config ensures that DDEV explicitly sets a valid timezone when building the container, preventing this issue from recurring.

## Laravel Timezone Configuration

Make sure your Laravel app's timezone in `config/app.php` is set correctly (it's already set to `'timezone' => 'UTC'` which is good).

If you need a different timezone, update both:
- `.ddev/config.yaml`: `timezone: Your/Timezone` (e.g., `timezone: America/New_York`)
- `config/app.php`: `'timezone' => 'Your/Timezone'`

## Still Having Issues?

If the problem persists after rebuilding:

1. **Check Docker status**: Make sure Docker is running
   ```bash
   docker ps
   ```

2. **Clear DDEV cache**:
   ```bash
   ddev poweroff
   docker system prune -a --volumes  # WARNING: This removes all unused Docker resources
   ddev start
   ```

3. **Check for custom PHP configuration**: Look for any custom PHP config that might interfere with timezone handling

4. **Update DDEV**: Make sure you're using the latest DDEV version
   ```bash
   brew upgrade ddev  # macOS
   # or
   ddev version
   ```

