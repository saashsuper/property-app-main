# Building-Specific Tabs Implementation - Block Inspection Edit Page

## 🎯 Feature Overview

Implemented **dynamic building-specific tabs** in the Block Inspection Edit page. Each building associated with a block now has its own dedicated accordion/tab for inspecting building-specific assets.

---

## 📋 What Was Built

### Before
- Only had **General Assets** tab (Gates, Street Lights, Landscape, Building Externals)

### After
- ✅ **General Assets** tab (unchanged)
- ✅ **Dynamic Building Tabs** - One tab per building in the block
- ✅ Each building tab contains 6 building-specific assets:
  1. **Stairs** (ID: 5)
  2. **Lights** (ID: 6)
  3. **Lifts** (ID: 7)
  4. **Walls** (ID: 8)
  5. **Fire Alarm** (ID: 9)
  6. **Doors/Fire Doors** (ID: 10)

---

## 🏗️ Architecture

### Database Structure

```
block_inspections
    ↓
block → buildings (BlockBuilding)
    ↓
building_assets (BlockBuildingAsset / BuildingAsset)
    ↓
block_inspection_assets (Stores inspection data)
    ↓
block_inspection_asset_images (Stores photos)
```

### Key Relationships

```php
// BlockInspection has many buildings through block
$blockInspection->block->buildings

// Each building can have inspection data for multiple assets
BlockInspectionAsset where:
  - block_inspection_id = inspection ID
  - block_building_id = building ID
  - building_asset_id = asset ID (5-10)
```

---

## 💻 Implementation Details

### 1. Controller Updates (`BlockInspectionController.php`)

#### A. `edit()` Method
**Added Code:**
```php
// Load buildings for the selected block
$buildings = $blockInspection->block->buildings()->orderBy('name')->get();

// Load building assets (assets that are specific to buildings)
// IDs 5-10: Stairs, Lights, Lifts, Walls, Fire Alarm, Doors/Fire Doors
$buildingAssets = \App\Models\BlockBuildingAsset::whereIn('id', [5, 6, 7, 8, 9, 10])
    ->orderBy('id')
    ->get();

// Load existing inspection assets data for building assets
// Group by building_id and asset_id for easy lookup
$existingBuildingInspectionAssets = BlockInspectionAsset::where('block_inspection_id', $blockInspection->id)
    ->whereNotNull('block_building_id')
    ->whereNotNull('building_asset_id')
    ->with(['buildingAsset', 'inspectionValue', 'images', 'blockBuilding'])
    ->get()
    ->groupBy(function($item) {
        return $item->block_building_id . '_' . $item->building_asset_id;
    });
```

**Data Passed to View:**
- `$buildings` - All buildings for the block
- `$buildingAssets` - The 6 building-specific assets
- `$existingBuildingInspectionAssets` - Previously saved inspection data

#### B. `update()` Method
**Added Code:**
```php
// Process Building Assets data
$this->processBuildingAssets($request, $blockInspection);
```

#### C. New `processBuildingAssets()` Method
**Purpose:** Process submitted data for all building assets

**Logic:**
```php
foreach ($buildings as $building) {
    foreach ($buildingAssets as $asset) {
        // Check for submitted data with naming convention:
        // building_{building_id}_asset_status_{asset_id}
        // building_{building_id}_notes_{asset_id}
        // building_{building_id}_photos_{asset_id}
        
        // Create/update BlockInspectionAsset record
        // Process and store uploaded photos
    }
}
```

**Field Naming Convention:**
- Status: `building_{building_id}_asset_status_{asset_id}`
- Notes: `building_{building_id}_notes_{asset_id}`
- Photos: `building_{building_id}_photos_{asset_id}[]`

**Example for Building ID 1, Asset ID 5 (Stairs):**
- Status: `building_1_asset_status_5`
- Notes: `building_1_notes_5`
- Photos: `building_1_photos_5[]`

---

### 2. View Updates (`resources/views/block-inspections/edit.blade.php`)

#### A. Dynamic Building Accordions

**Added After General Assets Tab:**
```blade
@foreach($buildings as $building)
<div class="accordion-item">
    <h2 class="accordion-header" id="building-{{ $building->id }}-header">
        <button class="accordion-button collapsed" type="button">
            <i class="ph-building me-3 text-info"></i>
            <span class="fw-bold">{{ strtoupper($building->name) }}</span>
        </button>
    </h2>
    <div id="building-{{ $building->id }}" class="accordion-collapse collapse">
        <div class="accordion-body">
            @foreach($buildingAssets as $asset)
                <!-- Asset inspection form for each asset -->
            @endforeach
        </div>
    </div>
</div>
@endforeach
```

#### B. Asset Inspection Form (Per Building, Per Asset)

**Structure:**
1. **Asset Name Header** - Shows asset name (e.g., "Stairs")
2. **Status Buttons** - Working / Not Working / N/A
3. **Dropzone Upload** - Drag & drop or click to browse
4. **Existing Images** - Thumbnails with delete buttons
5. **Notes Field** - Textarea with 500 char limit

**Example HTML:**
```html
<!-- Status Buttons -->
<input type="radio" name="building_1_asset_status_5" id="building_1_working_5" value="working">
<label for="building_1_working_5">Working</label>

<input type="radio" name="building_1_asset_status_5" id="building_1_not_working_5" value="not_working">
<label for="building_1_not_working_5">Not Working</label>

<input type="radio" name="building_1_asset_status_5" id="building_1_na_5" value="na">
<label for="building_1_na_5">N/A</label>

<!-- Dropzone -->
<div id="building_1_dropzone_5" class="dropzone"></div>

<!-- Notes -->
<textarea name="building_1_notes_5" maxlength="500"></textarea>
```

#### C. JavaScript - Dropzone Initialization

**For Each Building + Asset Combination:**
```javascript
@foreach($buildings as $building)
@foreach($buildingAssets as $asset)
const myDropzone = new Dropzone('#building_{{ $building->id }}_dropzone_{{ $asset->id }}', {
    url: '#',
    paramName: 'building_{{ $building->id }}_photos_{{ $asset->id }}',
    autoProcessQueue: false,
    uploadMultiple: true,
    maxFiles: 10,
    maxFilesize: 5,
    acceptedFiles: 'image/*',
    // ... configuration
});
@endforeach
@endforeach
```

#### D. JavaScript - Form Submission

**Added to FormData:**
```javascript
@foreach($buildings as $building)
@foreach($buildingAssets as $asset)
// Add files for this building asset
if (dropzoneFiles['building_{{ $building->id }}_{{ $asset->id }}']) {
    dropzoneFiles['building_{{ $building->id }}_{{ $asset->id }}'].forEach(function(file) {
        formData.append('building_{{ $building->id }}_photos_{{ $asset->id }}[]', file);
    });
}
@endforeach
@endforeach
```

---

## 🎨 User Interface

### Visual Structure

```
┌─────────────────────────────────────────────────────────┐
│  BLOCK INSPECTION EDIT PAGE                             │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  ▼ GENERAL INFORMATION                                  │
│     (Block, Inspector, Dates, Status, Notes)            │
│                                                         │
│  ▶ GENERAL ASSETS                                       │
│     • Gates                                             │
│     • Street Lights                                     │
│     • Landscape                                         │
│     • Building Externals                                │
│                                                         │
│  ▶ BUILDING A                    ← NEW!                │
│     • Stairs                                            │
│     • Lights                                            │
│     • Lifts                                             │
│     • Walls                                             │
│     • Fire Alarm                                        │
│     • Doors/Fire Doors                                  │
│                                                         │
│  ▶ BUILDING B                    ← NEW!                │
│     • Stairs                                            │
│     • Lights                                            │
│     • Lifts                                             │
│     • Walls                                             │
│     • Fire Alarm                                        │
│     • Doors/Fire Doors                                  │
│                                                         │
│  [Cancel]  [Update Inspection]                         │
└─────────────────────────────────────────────────────────┘
```

### Features Per Asset

```
┌─────────────────────────────────────────────────────────┐
│ Stairs                                                  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ Status: [Working] [Not Working] [N/A]                   │
│         🟢 Green  🔴 Red       🟢 Green                 │
│                                                         │
│ Photos:                                                 │
│ ┌───────────────────────────────┐                       │
│ │   ☁️ Drag & drop images      │                       │
│ │   or click to browse         │                       │
│ │   Max 5MB per image          │                       │
│ └───────────────────────────────┘                       │
│                                                         │
│ Existing: [img][img][img] ← Click X to delete          │
│                                                         │
│ Note: ┌──────────────────────┐                          │
│       │ Enter notes...       │                          │
│       │ (max 500 chars)      │                          │
│       └──────────────────────┘                          │
└─────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow

### Page Load (edit)

```
1. User opens: /block-inspections/{id}/edit
   ↓
2. Controller fetches:
   • blockInspection
   • blocks
   • users
   • generalAssets
   • existingInspectionAssets (general)
   • buildings ← NEW
   • buildingAssets ← NEW
   • existingBuildingInspectionAssets ← NEW
   ↓
3. View renders:
   • General Information accordion
   • General Assets accordion
   • One accordion per building ← NEW
     → Each with 6 building assets ← NEW
   ↓
4. JavaScript initializes:
   • Date pickers
   • General assets dropzones
   • Building assets dropzones ← NEW
```

### Form Submission (update)

```
1. User fills form and clicks "Update Inspection"
   ↓
2. JavaScript collects:
   • Form fields
   • General asset files
   • Building asset files ← NEW
   ↓
3. AJAX POST to: /block-inspections/{id}
   ↓
4. Controller processes:
   • General information (dates, status, etc.)
   • General assets (processGeneralAssets)
   • Building assets (processBuildingAssets) ← NEW
   ↓
5. For each building + asset:
   • Create/update BlockInspectionAsset record
   • Store uploaded photos
   ↓
6. Success response
   ↓
7. Redirect to inspections list
```

---

## 📁 Files Modified

### 1. Controller
**File:** `app/Http/Controllers/BlockInspectionController.php`

**Changes:**
- ✅ Updated `edit()` method (Lines 198-253)
- ✅ Updated `update()` method (Line 350)
- ✅ Added `processBuildingAssets()` method (Lines 656-756)

### 2. View
**File:** `resources/views/block-inspections/edit.blade.php`

**Changes:**
- ✅ Added building accordions (Lines 364-457)
- ✅ Added dropzone initialization for buildings (Lines 847-914)
- ✅ Added building files to form submission (Lines 982-993)

### 3. Models
**No changes needed** - All relationships already exist:
- `Block` → `buildings()` (hasMany)
- `BlockBuilding` (existing model)
- `BlockBuildingAsset` / `BuildingAsset` (existing)
- `BlockInspectionAsset` → `blockBuilding()` (belongsTo)
- `BlockInspectionAsset` → `buildingAsset()` (belongsTo)

---

## 🧪 Testing Guide

### Prerequisites
```bash
# Ensure DDEV is running
ddev start

# Ensure storage link exists
ddev exec php artisan storage:link

# Check block has buildings
ddev exec mysql -e "SELECT * FROM block_buildings WHERE block_id = (SELECT block_id FROM block_inspections WHERE id = 61);" db
```

### Test Scenario 1: Page Load
1. Navigate to: `https://proman.ddev.site/block-inspections/61/edit`
2. ✅ Page loads without errors
3. ✅ General Information accordion visible
4. ✅ General Assets accordion visible
5. ✅ Building-specific accordions visible (one per building)
6. ✅ Each building shows its name in the header
7. ✅ Click building accordion → expands to show 6 assets

### Test Scenario 2: Single Building Asset Inspection
1. Expand a building accordion (e.g., "BUILDING A")
2. For "Stairs" asset:
   - ✅ Select status: "Working"
   - ✅ Drag 2 images into dropzone
   - ✅ Images preview appears
   - ✅ Add notes: "Stairs cleaned and inspected"
   - ✅ Click "Update Inspection"
   - ✅ Success notification appears
   - ✅ Redirect to inspections list

### Test Scenario 3: Multiple Buildings
1. Expand "BUILDING A" accordion
   - Inspect "Stairs" → Status: Working
   - Upload 2 images
2. Expand "BUILDING B" accordion
   - Inspect "Lifts" → Status: Not Working
   - Upload 3 images
   - Add notes: "Lift requires maintenance"
3. Click "Update Inspection"
4. ✅ All data saved for both buildings
5. ✅ Return to edit page
6. ✅ Verify data persists for both buildings

### Test Scenario 4: Existing Images
1. Edit inspection that has existing building asset images
2. Expand building accordion
3. ✅ Existing images display below dropzone
4. ✅ Hover over image → Delete button appears
5. ✅ Click delete → Confirmation dialog
6. ✅ Confirm → Image removed
7. ✅ Refresh page → Image still deleted

### Test Scenario 5: Validation
1. Try uploading file > 5MB
2. ✅ Error: "File is too big"
3. Try uploading non-image (PDF)
4. ✅ Error: "You can't upload files of this type"
5. Try uploading 11 images
6. ✅ Error: "Maximum 10 files allowed"

### Test Scenario 6: All Asset Types
For each building, test all 6 assets:
1. ✅ Stairs - Working/Not Working/N/A
2. ✅ Lights - Working/Not Working/N/A
3. ✅ Lifts - Working/Not Working/N/A
4. ✅ Walls - Working/Not Working/N/A
5. ✅ Fire Alarm - Working/Not Working/N/A
6. ✅ Doors/Fire Doors - Working/Not Working/N/A

---

## 🔍 Database Verification

### Check Saved Data
```sql
-- View all building asset inspections
SELECT 
    bi.ref_no AS inspection,
    bb.name AS building,
    ba.name AS asset,
    biv.name AS status,
    bia.comments AS notes,
    COUNT(biai.id) AS image_count
FROM block_inspection_assets bia
JOIN block_inspections bi ON bi.id = bia.block_inspection_id
LEFT JOIN block_buildings bb ON bb.id = bia.block_building_id
LEFT JOIN building_assets ba ON ba.id = bia.building_asset_id
LEFT JOIN block_inspection_values biv ON biv.id = bia.block_inspection_value_id
LEFT JOIN block_inspection_asset_images biai ON biai.block_inspection_asset_id = bia.id
WHERE bia.block_building_id IS NOT NULL
GROUP BY bi.ref_no, bb.name, ba.name, biv.name, bia.comments
ORDER BY bi.id DESC, bb.name, ba.name;
```

### Check Images
```sql
-- View images for building assets
SELECT 
    bi.ref_no,
    bb.name AS building,
    ba.name AS asset,
    biai.image_name,
    biai.image_path,
    biai.created_at
FROM block_inspection_asset_images biai
JOIN block_inspection_assets bia ON bia.id = biai.block_inspection_asset_id
JOIN block_inspections bi ON bi.id = bia.block_inspection_id
LEFT JOIN block_buildings bb ON bb.id = bia.block_building_id
LEFT JOIN building_assets ba ON ba.id = bia.building_asset_id
WHERE bia.block_building_id IS NOT NULL
ORDER BY biai.created_at DESC;
```

---

## 💡 Key Features

### ✨ Dynamic Building Tabs
- Automatically creates one tab per building
- No hardcoding - works with any number of buildings
- Empty state if block has no buildings

### ✨ Building-Specific Assets
- Each building inspects the same 6 asset types
- Stairs, Lights, Lifts, Walls, Fire Alarm, Doors/Fire Doors
- Consistent with database asset IDs 5-10

### ✨ Identical UI to General Assets
- Same status buttons (Working/Not Working/N/A)
- Same dropzone functionality
- Same notes field
- Same image preview
- Same delete functionality

### ✨ Smart Data Persistence
- Loads existing inspection data on edit
- Pre-fills status selections
- Shows existing images
- Pre-fills notes

### ✨ Unique Field Names
- Prevents collisions between buildings
- Format: `building_{building_id}_asset_status_{asset_id}`
- Easy to parse in backend

---

## 🚀 Benefits

### For Users
- ✅ **Organized**: Each building has its own dedicated section
- ✅ **Clear**: Building name prominently displayed
- ✅ **Efficient**: Collapse/expand to focus on one building at a time
- ✅ **Consistent**: Same interface as General Assets

### For Developers
- ✅ **Maintainable**: Dynamic generation reduces code duplication
- ✅ **Scalable**: Works with any number of buildings
- ✅ **Flexible**: Easy to add more assets in future
- ✅ **Testable**: Clear data flow and naming conventions

### For Data
- ✅ **Structured**: Clear relationship between buildings and assets
- ✅ **Traceable**: Easy to query inspection data by building
- ✅ **Historical**: Preserves inspection history per building
- ✅ **Reportable**: Can generate building-specific reports

---

## 🔮 Future Enhancements

### Potential Improvements
1. **Bulk Actions**
   - Copy status from one building to all buildings
   - Mark all assets in building as "Working"

2. **Building Comparison**
   - Side-by-side view of multiple buildings
   - Highlight differences between buildings

3. **Asset-Specific Checklists**
   - Detailed checklist for lifts (e.g., emergency phone, certificate)
   - Fire alarm specific checks (zones, batteries, etc.)

4. **Summary View**
   - Dashboard showing status of all buildings at a glance
   - Color-coded status indicators

5. **Mobile Optimization**
   - Swipe between buildings
   - Larger touch targets for mobile inspectors

6. **Offline Mode**
   - Save drafts locally
   - Sync when connection restored

---

## 📞 Troubleshooting

### Issue: Building tabs not appearing
**Check:**
```bash
# Verify block has buildings
ddev exec mysql -e "SELECT * FROM block_buildings WHERE block_id = 1;" db
```
**Solution:** Block must have associated buildings in `block_buildings` table

### Issue: Dropzone not working for building assets
**Check:** Browser console for errors
**Solution:** Ensure Dropzone.js is loaded and no JavaScript errors

### Issue: Images not uploading for building assets
**Check:** Network tab - verify files in request payload
**Check:** Laravel logs: `storage/logs/laravel.log`
**Solution:** Check file permissions, upload limits

### Issue: Data not saving for building assets
**Check:** Browser console for submission errors
**Check:** Laravel logs for validation errors
**Solution:** Verify field names match backend expectations

---

## ✅ Completion Checklist

- [x] Controller fetches buildings and building assets
- [x] Controller processes building asset submissions
- [x] View renders dynamic building accordions
- [x] View includes dropzones for each building asset
- [x] JavaScript initializes dropzones for buildings
- [x] JavaScript adds building files to form submission
- [x] Status buttons work and change colors
- [x] Existing data loads correctly
- [x] Images upload successfully
- [x] Data persists after submission
- [x] No linter errors
- [x] Documentation created

---

## 📝 Summary

Successfully implemented **dynamic building-specific tabs** in the Block Inspection Edit page. Each building associated with a block now has its own dedicated accordion containing 6 building-specific assets (Stairs, Lights, Lifts, Walls, Fire Alarm, Doors/Fire Doors). The implementation follows the same pattern as General Assets with status buttons, dropzone uploads, notes, and image management.

**Result**: Inspectors can now inspect general assets AND building-specific assets in a single, organized interface! 🎉

