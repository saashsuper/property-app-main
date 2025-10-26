# Block Inspection Assets Implementation

## Overview
This document summarizes the implementation of General Assets data management for Block Inspections, including the creation of two new database tables: `block_inspection_assets` and `block_inspection_asset_images`.

## Database Schema

### Table: `block_inspection_assets`
**Location:** `database/migrations/2025_08_10_180200_create_block_inspection_assets_table.php`

This table stores inspection asset records with the following structure:
- `id` (bigint, primary key)
- `block_inspection_id` (bigint, foreign key → block_inspections)
- `block_building_id` (bigint, foreign key → block_buildings)
- `building_asset_id` (smallint, foreign key → building_assets)
- `block_inspection_value_id` (bigint, foreign key → block_inspection_values)
- `comments` (varchar 255)
- `timestamps` (created_at, updated_at)
- `softDeletes` (deleted_at)

**Note:** This migration and model already existed in the codebase.

### Table: `block_inspection_asset_images` ✨ NEW
**Location:** `database/migrations/2025_08_10_180201_create_block_inspection_asset_images_table.php`

This table stores images associated with inspection assets:
- `id` (bigint, primary key)
- `block_inspection_asset_id` (bigint, foreign key → block_inspection_assets)
- `block_inspection_id` (bigint, foreign key → block_inspections)
- `block_building_id` (bigint, foreign key → block_buildings)
- `building_asset_id` (integer, nullable, foreign key → building_assets)
- `image_path` (varchar 255, nullable) - Directory path where image is stored
- `image_name` (varchar 100, nullable) - Actual filename
- `s3_status` (smallint, default 0) - Upload status (0 = local, 1 = uploaded to S3)
- `timestamps` (created_at, updated_at)
- `softDeletes` (deleted_at)

**Indexes:**
- Primary key on `id`
- Foreign keys with cascade delete on all relationship fields
- Individual indexes on all foreign keys for query optimization

## Models

### BlockInspectionAsset ✨ UPDATED
**Location:** `app/Models/BlockInspectionAsset.php`

**Added Relationship:**
```php
public function images()
{
    return $this->hasMany(BlockInspectionAssetImage::class);
}
```

**Existing Relationships:**
- `blockInspection()` - belongsTo BlockInspection
- `blockBuilding()` - belongsTo BlockBuilding
- `buildingAsset()` - belongsTo BuildingAsset
- `inspectionValue()` - belongsTo BlockInspectionValue

### BlockInspectionAssetImage ✨ NEW
**Location:** `app/Models/BlockInspectionAssetImage.php`

**Fillable Fields:**
- block_inspection_asset_id
- block_inspection_id
- block_building_id
- building_asset_id
- image_path
- image_name
- s3_status

**Relationships:**
- `blockInspectionAsset()` - belongsTo BlockInspectionAsset
- `blockInspection()` - belongsTo BlockInspection
- `blockBuilding()` - belongsTo BlockBuilding
- `buildingAsset()` - belongsTo BuildingAsset

**Features:**
- Uses `HasFactory` trait for testing
- Uses `SoftDeletes` trait for safe deletion
- Proper type casting for all integer fields

## Factory

### BlockInspectionAssetImageFactory ✨ NEW
**Location:** `database/factories/BlockInspectionAssetImageFactory.php`

**Default State:**
- Generates unique UUID-based filenames
- Creates appropriate directory structure: `inspection-assets/{uuid}.jpg`
- Defaults to s3_status = 0 (not uploaded)
- Links to appropriate related models via factories

**State Methods:**
- `uploaded()` - Marks image as uploaded to S3 (s3_status = 1)
- `png()` - Creates PNG image instead of JPG
- `withStatus($status)` - Sets custom s3_status value

## Seeder

### BlockInspectionAssetImageSeeder ✨ NEW
**Location:** `database/seeders/BlockInspectionAssetImageSeeder.php`

**Features:**
- Creates 1-3 sample images per block inspection asset
- Uses realistic image names (e.g., 'asset_overview.jpg', 'damage_inspection.jpg')
- Generates unique timestamped filenames
- Creates proper directory structure: `inspection-assets/{inspection_id}/{asset_id}`
- Randomly sets s3_status (0 or 1) for testing purposes
- Skips assets that already have images
- Includes helper method `createImagesForAsset()` for targeted seeding

**Sample Image Names:**
- asset_overview.jpg
- asset_detail.jpg
- asset_condition.jpg
- damage_inspection.jpg
- wear_tear_check.jpg
- maintenance_status.jpg
- safety_inspection.jpg
- compliance_check.jpg
- before_repair.jpg
- after_repair.jpg
- close_up_view.jpg
- wide_angle_view.jpg
- installation_check.jpg
- functionality_test.jpg
- documentation_photo.jpg

## DatabaseSeeder Updates ✨ UPDATED

**Added to seeder call chain:**
```php
BlockInspectionAssetImageSeeder::class,
```
Position: After BlockInspectionSeeder, before BlockIssuesSeeder

**Added to clearExistingData() method:**
```php
'block_inspection_asset_images', // Added before block_inspection_assets
```

## File Structure

```
/Users/vijeesh/LaravelApps/property-app-main/
├── app/Models/
│   ├── BlockInspectionAsset.php (✨ UPDATED - added images relationship)
│   └── BlockInspectionAssetImage.php (✨ NEW)
├── database/
│   ├── migrations/
│   │   ├── 2025_08_10_180200_create_block_inspection_assets_table.php (✅ EXISTING)
│   │   └── 2025_08_10_180201_create_block_inspection_asset_images_table.php (✨ NEW)
│   ├── factories/
│   │   └── BlockInspectionAssetImageFactory.php (✨ NEW)
│   └── seeders/
│       ├── BlockInspectionAssetImageSeeder.php (✨ NEW)
│       └── DatabaseSeeder.php (✨ UPDATED)
```

## Usage Examples

### Creating an Inspection Asset Image
```php
use App\Models\BlockInspectionAssetImage;

$image = BlockInspectionAssetImage::create([
    'block_inspection_asset_id' => 1,
    'block_inspection_id' => 1,
    'block_building_id' => 1,
    'building_asset_id' => 5,
    'image_path' => 'inspection-assets/1/1',
    'image_name' => '1634567890_abc123_asset_view.jpg',
    's3_status' => 0,
]);
```

### Accessing Images from Asset
```php
use App\Models\BlockInspectionAsset;

$asset = BlockInspectionAsset::find(1);
$images = $asset->images;

foreach ($images as $image) {
    echo $image->image_path . '/' . $image->image_name;
}
```

### Using Factory in Tests
```php
use App\Models\BlockInspectionAssetImage;

// Create a single image
$image = BlockInspectionAssetImage::factory()->create();

// Create an uploaded PNG image
$uploadedImage = BlockInspectionAssetImage::factory()
    ->png()
    ->uploaded()
    ->create();

// Create multiple images
$images = BlockInspectionAssetImage::factory()->count(5)->create();
```

### Running Seeders
```bash
# Run all seeders (includes BlockInspectionAssetImageSeeder)
php artisan db:seed

# Run only the inspection asset image seeder
php artisan db:seed --class=BlockInspectionAssetImageSeeder
```

## Running Migrations

```bash
# Run the new migration
php artisan migrate

# Rollback if needed
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

## Data Flow

1. **Inspection Creation** → BlockInspection created
2. **Asset Assignment** → BlockInspectionAsset created (links inspection to building asset)
3. **Image Upload** → BlockInspectionAssetImage created (stores image metadata)
4. **S3 Upload** → s3_status updated from 0 to 1
5. **Image Retrieval** → Access via BlockInspectionAsset::images() relationship

## S3 Status Codes

- `0` - Image stored locally, not uploaded to S3 yet
- `1` - Image successfully uploaded to S3
- Other values can be used for additional states (e.g., 2 = failed upload, 3 = deleted from S3)

## Key Design Decisions

1. **Soft Deletes**: Both tables use soft deletes to maintain data integrity and audit trail
2. **Foreign Key Cascades**: All foreign keys cascade on delete to maintain referential integrity
3. **Separate Path and Name**: image_path and image_name are stored separately for flexibility in storage organization
4. **S3 Status Tracking**: Allows tracking upload status for async S3 operations
5. **Comprehensive Indexing**: All foreign keys are indexed for optimal query performance
6. **Nullable Building Asset**: building_asset_id is nullable to allow for general asset images not tied to specific building assets

## Testing Checklist

- [x] Migration runs successfully
- [x] Model relationships work correctly
- [x] Factory generates valid test data
- [x] Seeder creates sample data
- [x] No linting errors
- [x] Foreign key constraints enforced
- [x] Soft deletes function properly
- [x] DatabaseSeeder properly includes new seeder

## Next Steps

1. **Controller Implementation**: Create controllers for managing inspection asset images
2. **API Endpoints**: Design RESTful endpoints for CRUD operations
3. **File Upload Handler**: Implement image upload functionality
4. **S3 Integration**: Add background job for uploading images to S3
5. **Frontend UI**: Create interface for uploading and viewing inspection asset images
6. **Validation Rules**: Add validation for image types, sizes, and required fields
7. **Tests**: Write unit and feature tests for the new functionality

## Related Documentation

- Refer to `saashmagna_apm.sql` for original schema reference (lines 149-178)
- See `BLOCK_EDIT_FEATURE_ANALYSIS.md` for inspection edit feature details
- Check `SEEDERS_ANALYSIS.md` for seeder patterns and best practices

---

**Created:** October 18, 2025  
**Status:** ✅ Complete - Ready for controller and UI implementation

