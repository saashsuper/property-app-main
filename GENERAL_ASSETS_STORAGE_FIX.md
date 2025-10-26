# General Assets Storage Fix

## Issue Description
General assets data was appearing to not store properly in the `block_inspection_assets` table when editing block inspections. Users could not see their general assets data after saving.

## Root Cause Analysis

After investigating the logs and code, we discovered that:

1. **Data WAS being saved correctly** to the database - the logs showed successful creation/update of `block_inspection_assets` records
2. **The issue was in the display layer** - the show view was not configured to display general assets, only building assets
3. **Image storage had a bug** - when uploading images for general assets, the code was trying to use the general asset ID as a `building_asset_id`, which could cause foreign key constraint violations

## Changes Made

### 1. Fixed Block Inspection Show View
**File**: `resources/views/block-inspections/show.blade.php`

**Changes**:
- Added a "Type" column to distinguish between General Assets and Building Assets
- Updated the "Asset" column to check for `generalAsset` in addition to `buildingAsset`
- Added an "Images" column to display uploaded images
- Added proper conditional logic to display the correct asset name based on type:
  ```php
  @if($asset->block_general_asset_id)
      {{ $asset->generalAsset->name ?? 'N/A' }}
  @else
      {{ $asset->buildingAsset->name ?? 'N/A' }}
  @endif
  ```

### 2. Updated Controller to Load General Assets
**File**: `app/Http/Controllers/BlockInspectionController.php` - `show()` method

**Changes**:
- Added eager loading for general assets relationship: `'inspectionAssets.generalAsset'`
- Added loading for related data: `'inspectionAssets.images'`, `'inspectionAssets.blockBuilding'`, `'inspectionAssets.inspectionValue.valueType'`

### 3. Fixed Image Storage Bug
**File**: `app/Http/Controllers/BlockInspectionController.php` - `processAssetImages()` method

**Changes**:
- Fixed the `building_asset_id` assignment to be NULL for general assets
- Added logic to detect if the inspection asset is a general asset:
  ```php
  $buildingAssetId = $inspectionAsset->block_general_asset_id ? null : $assetId;
  ```
- This prevents foreign key constraint violations when uploading images for general assets

## Testing Steps

1. Navigate to a block inspection edit page: `https://proman.ddev.site/block-inspections/61/edit`
2. Fill in general assets data (Gates, Landscape, Street Lights, Building Externals)
3. Optionally upload images for general assets
4. Save the inspection
5. View the inspection details page
6. Verify that:
   - General assets are displayed in the "Inspection Assets" table
   - Assets show the correct type badge (General Asset vs Building Asset)
   - Status, comments, and images are displayed correctly
7. Edit the inspection again and verify existing data is pre-filled in the form

## Database Schema

The fix works with the existing schema:

**block_inspection_assets** table:
- `block_inspection_id` - Link to the inspection
- `block_building_id` - Can be NULL for general assets
- `building_asset_id` - NULL for general assets
- `block_general_asset_id` - Set for general assets, NULL for building assets
- `block_inspection_value_id` - The inspection value (Good, Fair, Poor, etc.)
- `comments` - Optional notes

**block_inspection_asset_images** table:
- `block_inspection_asset_id` - Link to the inspection asset
- `block_inspection_id` - Link to the inspection
- `block_building_id` - Can be NULL for general assets
- `building_asset_id` - NULL for general assets (fixed in this update)
- `image_path` - Storage path
- `image_name` - Filename

## Key Points

1. The `updateOrCreate` logic in `processGeneralAssets()` was working correctly all along
2. The data persistence issue was actually a display issue, not a storage issue
3. Images can now be properly saved for general assets without foreign key errors
4. Both general assets and building assets are now displayed properly in the show view
5. The edit form was already working correctly for loading existing general assets data

## Related Files

- `app/Http/Controllers/BlockInspectionController.php` - Main controller handling inspection CRUD
- `app/Models/BlockInspectionAsset.php` - Model for inspection assets
- `app/Models/BlockInspectionAssetImage.php` - Model for asset images
- `resources/views/block-inspections/show.blade.php` - Show view
- `resources/views/block-inspections/edit.blade.php` - Edit form (no changes needed)

