# Block Inspection Assets - Update Implementation

## Overview
This document describes the implementation of General Assets data insertion/update functionality when editing Block Inspections from the inspection edit page (`block-inspections/{id}/edit`).

## Implementation Date
**October 18, 2025**

## What Was Implemented

### 1. Controller Updates (`BlockInspectionController.php`)

#### Added Model Imports
```php
use App\Models\BlockInspectionAsset;
use App\Models\BlockInspectionAssetImage;
use App\Models\BlockInspectionValue;
use App\Models\BlockGeneralAsset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
```

#### Updated `update()` Method
- Added call to `processGeneralAssets()` after updating inspection data
- Enhanced validation rules to include dynamic asset fields
- Validates asset status, notes, and image uploads

#### New Method: `processGeneralAssets()`
**Purpose:** Process and save general assets data from the edit form

**Functionality:**
- Retrieves all general assets from the database
- Gets the first building of the block for asset association
- Maps form status values to inspection value IDs
- For each asset with submitted data:
  - Creates or updates `BlockInspectionAsset` record
  - Processes and stores uploaded images
  
**Key Features:**
- Uses `updateOrCreate()` to handle both insert and update operations
- Skips assets with no status selected
- Associates assets with inspection, building, and asset type
- Stores comments/notes for each asset

#### New Method: `processAssetImages()`
**Purpose:** Handle file uploads and create image records

**Functionality:**
- Generates unique timestamped filenames
- Stores images in organized directory structure
- Creates `BlockInspectionAssetImage` database records
- Sets initial S3 status to 0 (local storage)

**File Naming Convention:**
```
{timestamp}_{random_hash}_asset_{asset_id}.{extension}
```

**Storage Path Structure:**
```
storage/app/public/inspection-assets/{inspection_id}/{asset_id}/
```

#### New Method: `getStatusToValueMap()`
**Purpose:** Map form status values to database inspection value IDs

**Mappings:**
- `working` → "Good" (ID: 2) or "Operational" (ID: 6)
- `not_working` → "Poor" (ID: 4) or "Non-Operational" (ID: 8)
- `na` → "Fair" (ID: 3) as neutral/not applicable status

### 2. View Updates (`edit.blade.php`)

#### Form Enhancement
Added `enctype="multipart/form-data"` to the form tag to enable file uploads:

```php
<form action="{{ route('block-inspections.update', $blockInspection->id) }}" 
      method="POST" 
      enctype="multipart/form-data">
```

#### Existing General Assets UI
The form already includes:
- Status/condition radio buttons for each asset
- Drag & drop photo upload areas
- Notes textarea (max 500 characters)
- Proper field naming convention

### 3. Validation Rules

Dynamic validation added for each general asset:

```php
"asset_status_{assetId}" => 'nullable|in:working,not_working,na'
"notes_{assetId}" => 'nullable|string|max:500'
"photos_{assetId}" => 'nullable|array'
"photos_{assetId}.*" => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120'
```

**Constraints:**
- Status must be one of: working, not_working, na
- Notes limited to 500 characters
- Images must be: jpeg, jpg, png, or gif
- Maximum image size: 5MB (5120 KB)

## Database Tables

### `block_inspection_assets`
Stores asset inspection records:
- `block_inspection_id` - Links to inspection
- `block_building_id` - Links to building
- `building_asset_id` - Links to asset type (general asset)
- `block_inspection_value_id` - Status/condition value
- `comments` - Notes from the form

### `block_inspection_asset_images`
Stores asset image metadata:
- `block_inspection_asset_id` - Links to asset record
- `block_inspection_id` - Links to inspection
- `block_building_id` - Links to building
- `building_asset_id` - Asset type reference
- `image_path` - Directory path (without filename)
- `image_name` - Actual filename
- `s3_status` - Upload status (0 = local, 1 = S3)

## Data Flow

### When User Submits the Edit Form:

1. **Form Submission**
   - User fills in asset status, uploads photos, adds notes
   - Form data includes: `asset_status_1`, `photos_1[]`, `notes_1`, etc.

2. **Validation**
   - Controller validates all fields including dynamic asset fields
   - Checks image types, sizes, and format

3. **Inspection Update**
   - Basic inspection data (dates, status, notes) updated first
   - Team members updated

4. **Assets Processing** (`processGeneralAssets()`)
   - For each general asset with data:
     - Map status to inspection value ID
     - Create or update `block_inspection_assets` record
     - If images uploaded, call `processAssetImages()`

5. **Image Processing** (`processAssetImages()`)
   - Generate unique filename for each image
   - Store in `storage/app/public/inspection-assets/{inspection_id}/{asset_id}/`
   - Create `block_inspection_asset_images` record with metadata

6. **Response**
   - Redirect back to index or show page
   - Display success message

## Form Field Naming Convention

### Status Radio Buttons
```html
<input type="radio" name="asset_status_{asset_id}" value="working">
<input type="radio" name="asset_status_{asset_id}" value="not_working">
<input type="radio" name="asset_status_{asset_id}" value="na">
```

### Photo Upload
```html
<input type="file" name="photos_{asset_id}[]" multiple accept="image/*">
```

### Notes
```html
<textarea name="notes_{asset_id}" maxlength="500"></textarea>
```

## Example Usage

### Submitting Asset Data

When editing inspection ID #12:

**Form Data:**
```
block_id: 5
scheduled_date: 18/10/2025
scheduled_time: 10:00
asset_status_1: working
notes_1: "Gates are functioning properly"
photos_1[0]: [UploadedFile: gate_front.jpg]
photos_1[1]: [UploadedFile: gate_mechanism.jpg]
asset_status_2: not_working
notes_2: "Street lights on west side need replacement"
photos_2[0]: [UploadedFile: broken_light.jpg]
```

**Database Records Created:**

`block_inspection_assets`:
```
id: 1
block_inspection_id: 12
block_building_id: 15
building_asset_id: 1 (Gates)
block_inspection_value_id: 2 (Good)
comments: "Gates are functioning properly"
```

```
id: 2
block_inspection_id: 12
block_building_id: 15
building_asset_id: 2 (Street Lights)
block_inspection_value_id: 4 (Poor)
comments: "Street lights on west side need replacement"
```

`block_inspection_asset_images`:
```
id: 1
block_inspection_asset_id: 1
block_inspection_id: 12
block_building_id: 15
building_asset_id: 1
image_path: "inspection-assets/12/1"
image_name: "1729245600_a7b3c9d2_asset_1.jpg"
s3_status: 0
```

```
id: 2
block_inspection_asset_id: 1
block_inspection_id: 12
block_building_id: 15
building_asset_id: 1
image_path: "inspection-assets/12/1"
image_name: "1729245601_d4e8f1a3_asset_1.jpg"
s3_status: 0
```

```
id: 3
block_inspection_asset_id: 2
block_inspection_id: 12
block_building_id: 15
building_asset_id: 2
image_path: "inspection-assets/12/2"
image_name: "1729245602_b6c2e7f9_asset_2.jpg"
s3_status: 0
```

## Key Features

### ✅ Insert and Update Support
- Uses `updateOrCreate()` method
- Automatically handles both new and existing records
- Updates existing assets when editing inspection multiple times

### ✅ Multiple Images Per Asset
- Each asset can have multiple images
- Images stored with unique filenames
- Organized directory structure

### ✅ Flexible Status Mapping
- Dynamic mapping to inspection values
- Supports different value types
- Fallback to default values if specific ones not found

### ✅ Building Association
- Currently uses first building of the block
- Can be extended to support multiple buildings
- Building ID stored with each asset and image

### ✅ Validation
- Comprehensive validation for all fields
- Image type and size restrictions
- Character limits on notes

## Future Enhancements

### 1. Multiple Building Support
Currently, assets are associated with the first building. Future enhancement:
- Add building selector in the UI
- Allow asset inspection per building
- Group assets by building in the form

### 2. Image Preview
Add JavaScript to show image previews after upload:
- Thumbnail display
- Remove/replace functionality
- Drag to reorder images

### 3. S3 Upload Integration
Implement background job for S3 upload:
- Queue job after image storage
- Update `s3_status` after successful upload
- Handle upload failures gracefully

### 4. Edit Existing Assets
Add UI to edit already saved assets:
- Load existing asset data into form
- Display saved images with delete option
- Update vs. create indicators

### 5. Asset Image Gallery
Create view to display all images for an asset:
- Lightbox/modal viewer
- Download functionality
- Print-friendly version

### 6. Bulk Asset Operations
Add ability to:
- Apply same status to multiple assets
- Copy notes across assets
- Batch upload images

## Testing Checklist

- [ ] Submit form with asset status only (no photos, no notes)
- [ ] Submit form with all fields populated
- [ ] Upload single image per asset
- [ ] Upload multiple images per asset
- [ ] Test with maximum image size (5MB)
- [ ] Test with invalid image types
- [ ] Test with notes at 500 character limit
- [ ] Edit same inspection twice (test update functionality)
- [ ] Verify image storage location
- [ ] Verify database records created correctly
- [ ] Test with no assets selected
- [ ] Test with partial asset data

## Troubleshooting

### Images Not Uploading
1. Check `storage/app/public` is linked: `php artisan storage:link`
2. Verify directory permissions
3. Check PHP upload_max_filesize and post_max_size settings
4. Confirm form has `enctype="multipart/form-data"`

### Invalid Inspection Value IDs
1. Check `block_inspection_values` table has required values
2. Verify mapping in `getStatusToValueMap()` method
3. Ensure fallback IDs exist

### Assets Not Saving
1. Verify block has at least one building
2. Check asset status is being submitted
3. Review validation errors
4. Check database foreign key constraints

## Files Modified

1. ✅ `app/Http/Controllers/BlockInspectionController.php`
   - Added imports
   - Updated `update()` method
   - Added `processGeneralAssets()` method
   - Added `processAssetImages()` method
   - Added `getStatusToValueMap()` method

2. ✅ `resources/views/block-inspections/edit.blade.php`
   - Added `enctype="multipart/form-data"` to form

## Related Documentation

- `INSPECTION_ASSETS_IMPLEMENTATION.md` - Database schema and models
- `INSPECTION_ASSETS_EXECUTION_SUMMARY.md` - Initial setup summary
- `BLOCK_EDIT_FEATURE_ANALYSIS.md` - Feature analysis

---

**Status:** ✅ **COMPLETE AND READY FOR TESTING**

The implementation is complete and ready for testing in the development environment.

