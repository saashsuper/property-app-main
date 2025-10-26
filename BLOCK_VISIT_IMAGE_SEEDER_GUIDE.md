# Block Visit Image Seeder Guide

## Overview
This guide explains how to use the `BlockVisitImageSeeder` to populate the new `image_name` column added by the migration `2025_10_11_064455_add_image_name_to_block_visit_images_table.php`.

---

## What the Seeder Does

The `BlockVisitImageSeeder` performs two main tasks:

1. **Updates Existing Records**: Migrates old image records to the new format by extracting the filename from the full `image_path` and populating the new `image_name` column.

2. **Creates Sample Data**: Generates realistic sample block visit images for testing and development purposes.

---

## Migration Context

### Before Migration
```
image_path: "block-visits/123/image.jpg"  (full path with filename)
image_name: NULL
```

### After Migration
```
image_path: "block-visits/123"            (directory only)
image_name: "1234567890_abc12345_image.jpg"  (timestamped filename)
```

---

## Running the Seeder

### Option 1: Run All Seeders (Recommended)
```bash
ddev php artisan db:seed
```
This runs the complete database seeding including `BlockVisitImageSeeder`.

### Option 2: Run Only This Seeder
```bash
ddev php artisan db:seed --class=BlockVisitImageSeeder
```

### Option 3: Run with Fresh Migration
```bash
ddev php artisan migrate:fresh --seed
```

---

## Seeder Features

### 1. Update Existing Records
```php
// Automatically updates existing block_visit_images records
// Extracts filename from old format and populates image_name

Old Format:
- image_path: "block-visits/123/photo.jpg"
- image_name: null

New Format:
- image_path: "block-visits/123"
- image_name: "photo.jpg"
```

### 2. Create Sample Images
```php
// Creates 1-3 sample images per block visit
// Uses realistic image names like:
- entrance_view.jpg
- lobby_area.jpg
- exterior_front.jpg
- roof_inspection.jpg
- hvac_system.jpg
etc.
```

### 3. Targeted Image Creation
```php
// You can create images for a specific block visit
$seeder = new BlockVisitImageSeeder();
$seeder->createImagesForVisit($blockVisitId, $imageCount);
```

---

## Sample Image Names

The seeder includes 20 realistic image names:

| Category | Image Names |
|----------|-------------|
| **Exterior** | entrance_view.jpg, exterior_front.jpg, exterior_back.jpg, facade_inspection.jpg |
| **Interior** | lobby_area.jpg, common_area.jpg, stairwell_condition.jpg |
| **Systems** | hvac_system.jpg, electrical_panel.jpg, plumbing_overview.jpg, drainage_system.jpg |
| **Safety** | fire_safety_equipment.jpg, emergency_exits.jpg, security_systems.jpg |
| **Structural** | roof_inspection.jpg, basement_check.jpg, elevator_inspection.jpg |
| **Outdoor** | parking_lot.jpg, landscaping_view.jpg, lighting_overview.jpg |

---

## File Structure

### Seeder Location
```
database/seeders/BlockVisitImageSeeder.php
```

### Model Used
```
app/Models/BlockVisitImage.php
```

### Migration File
```
database/migrations/2025_10_11_064455_add_image_name_to_block_visit_images_table.php
```

---

## Database Schema

### block_visit_images Table
```sql
id                  BIGINT (Primary Key)
block_visit_id      BIGINT (Foreign Key)
image_path          VARCHAR(255)  -- Directory path only (NEW)
image_name          VARCHAR(255)  -- Filename (ADDED BY MIGRATION)
s3_status           SMALLINT      -- 0: Not uploaded, 1: Uploaded
deleted_at          TIMESTAMP
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

## Usage Examples

### Example 1: Seed All Data
```bash
# Fresh install with all seeders
ddev php artisan migrate:fresh --seed
```

**Output:**
```
Migrating: 2025_10_11_064455_add_image_name_to_block_visit_images_table
Migrated:  2025_10_11_064455_add_image_name_to_block_visit_images_table

Seeding: BlockVisitSeeder
BlockVisit seeder completed successfully. Created 50 block visits.

Seeding: BlockVisitImageSeeder
Updated 0 existing block visit image records.
Created 87 sample block visit images.
```

### Example 2: Update Only Existing Records
```php
// In tinker or a custom command
use Database\Seeders\BlockVisitImageSeeder;

$seeder = new BlockVisitImageSeeder();
$seeder->updateExistingRecords();
```

### Example 3: Create Images for Specific Visit
```php
// Create 5 images for block visit ID 10
use Database\Seeders\BlockVisitImageSeeder;

$seeder = new BlockVisitImageSeeder();
$seeder->createImagesForVisit(10, 5);
```

---

## Model Methods

The `BlockVisitImage` model includes helper methods for the new structure:

### 1. Get Image URL
```php
$image->image_url
// Returns: asset('storage/block-visits/123/1234567890_abc12345_photo.jpg')
```

### 2. Get Display Name
```php
$image->display_name
// Returns: "photo.jpg" (without timestamp prefix)
```

### 3. Relationship
```php
$image->visit  // Returns the associated BlockVisit
```

---

## Testing the Seeder

### Step 1: Run Migration
```bash
ddev php artisan migrate
```

### Step 2: Run Seeder
```bash
ddev php artisan db:seed --class=BlockVisitImageSeeder
```

### Step 3: Verify Data
```bash
ddev php artisan tinker
```

```php
// Check total images
\App\Models\BlockVisitImage::count();

// Check images with new format
\App\Models\BlockVisitImage::whereNotNull('image_name')->count();

// View sample records
\App\Models\BlockVisitImage::with('visit')->latest()->take(5)->get();

// Check image URLs
$image = \App\Models\BlockVisitImage::first();
echo $image->image_url;
echo $image->display_name;
```

---

## Troubleshooting

### Issue 1: No Block Visits Found
```
Warning: No block visits found. Skipping sample image creation.
```

**Solution:** Run `BlockVisitSeeder` first:
```bash
ddev php artisan db:seed --class=BlockVisitSeeder
ddev php artisan db:seed --class=BlockVisitImageSeeder
```

### Issue 2: Images Already Exist
The seeder skips block visits that already have images. To reseed:

```bash
# Clear existing images
ddev php artisan tinker
\App\Models\BlockVisitImage::truncate();
exit

# Run seeder again
ddev php artisan db:seed --class=BlockVisitImageSeeder
```

### Issue 3: Path Format Issues
If you see errors with image paths:

```bash
# Check current format
ddev php artisan tinker
\App\Models\BlockVisitImage::select('image_path', 'image_name')->take(10)->get();
```

---

## Best Practices

1. **Run After Block Visits**: Always ensure block visits exist before running this seeder.

2. **Backup Before Migration**: 
   ```bash
   ddev export-db
   ```

3. **Test in Development**: Test the seeder in development before running in production.

4. **Use Transactions**: The seeder automatically uses database transactions for safety.

5. **Monitor Output**: Pay attention to seeder output for counts and warnings.

---

## Integration with DatabaseSeeder

The `BlockVisitImageSeeder` is integrated into the main `DatabaseSeeder`:

```php
// database/seeders/DatabaseSeeder.php
$this->call([
    // ... other seeders
    BlockVisitSeeder::class,        // Creates block visits
    BlockVisitImageSeeder::class,   // Creates images for visits
    BlockInspectionSeeder::class,
]);
```

**Order matters!** The seeder must run after `BlockVisitSeeder`.

---

## Production Use

### For Existing Data (Production)
```bash
# 1. Run migration
ddev php artisan migrate

# 2. Run seeder to update existing records only
ddev php artisan db:seed --class=BlockVisitImageSeeder

# The seeder will:
# - Update existing records to new format
# - Skip creating sample data if records already have images
```

### For New Installation (Development)
```bash
# Run complete seeding
ddev php artisan migrate:fresh --seed
```

---

## Related Files

| File | Purpose |
|------|---------|
| `database/seeders/BlockVisitImageSeeder.php` | Main seeder class |
| `database/seeders/BlockVisitSeeder.php` | Creates block visits |
| `database/migrations/2025_10_11_064455_add_image_name_to_block_visit_images_table.php` | Adds image_name column |
| `app/Models/BlockVisitImage.php` | Model with helper methods |
| `database/seeders/DatabaseSeeder.php` | Main seeder orchestrator |

---

## Summary

✅ **Updates** existing image records to new format  
✅ **Creates** sample images for testing  
✅ **Maintains** data integrity with soft deletes  
✅ **Supports** targeted image creation  
✅ **Integrates** with DatabaseSeeder  
✅ **Provides** helpful console output  

---

**Created:** October 12, 2025  
**Migration:** 2025_10_11_064455  
**Laravel Version:** 10.x

