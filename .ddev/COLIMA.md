# Using DDEV with Colima

This project is configured to work with [Colima](https://github.com/abiosoft/colima) as the Docker provider on macOS.

## Quick start

1. **Ensure Colima is running and Docker context is set:**
   ```bash
   ddev colima-start
   ```
   (Or manually: `colima start` then `docker context use colima`)

2. **Start DDEV:**
   ```bash
   ddev start
   ```

3. Open the project URL (e.g. **https://proman.ddev.site** or **http://proman.ddev.site:8080**).

## First-time Colima setup (if not done yet)

```bash
# Install Colima and Docker client (Homebrew)
brew install colima docker

# Start Colima with recommended resources (4 CPU, 6GB RAM, 100GB disk)
colima start --cpu 4 --memory 6 --disk 100 --vm-type=vz --mount-type=virtiofs --dns=1.1.1.1

# Use Colima as Docker context
docker context use colima
```

## Troubleshooting

- **"Could not connect to a Docker provider"**  
  Colima is not running or Docker context is wrong. Run `ddev colima-start` or `colima start` then `docker context use colima`.

- **After reboot**  
  Run `colima start` (or `ddev colima-start`) before `ddev start`.

- **Project must be under your home directory** for Colima’s default mounts to work (this project is).
