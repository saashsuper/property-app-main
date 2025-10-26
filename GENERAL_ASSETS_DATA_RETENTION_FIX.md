# General Assets Data Retention Fix

## Issue
After submitting the block inspection edit form, the general assets data was saving to the database, but when reopening the edit page, the values were not retained (radio buttons not pre-selected, notes not shown, colors not applied).

## Root Cause
The edit page was not loading or displaying the existing inspection assets data for general assets. The controller was loading general assets definitions but not the saved inspection data.

## Solution

### 1. Controller Updates

#### `BlockInspectionController::edit()` Method
**Added:**
- Query to load existing inspection assets for the current inspection
- Filtered by general assets only (`whereNotNull('block_general_asset_id')`)
- Eager-loaded related data (generalAsset, inspectionValue, images)
- Keyed the collection by `block_general_asset_id` for easy lookup in the view

**Code:**
```php
// Load existing inspection assets data for general assets
$existingInspectionAssets = BlockInspectionAsset::where('block_inspection_id', $blockInspection->id)
    ->whereNotNull('block_general_asset_id')
    ->with(['generalAsset', 'inspectionValue', 'images'])
    ->get()
    ->keyBy('block_general_asset_id');
```

#### New Helper Method: `getValueToStatusMap()`
**Added:**
- Public static method to map inspection value IDs back to form status values
- Reverse mapping of the existing `getStatusToValueMap()` method
- Maps: Good/Operational → 'working', Poor/Non-Operational → 'not_working', others → 'na'

### 2. View Updates

#### `resources/views/block-inspections/edit.blade.php`

**Added PHP Logic:**
- For each general asset, check if existing data exists
- Map the saved inspection value ID to form status (working/not_working/na)
- Extract existing notes

**Updated Form Elements:**
- Radio buttons: Added `checked` attribute when status matches
- Textarea: Pre-filled with existing notes
- Applied to all three radio options: working, not_working, na

**Example:**
```blade
@php
    $existingData = $existingInspectionAssets->get($asset->id);
    $selectedStatus = $existingData ? \App\Http\Controllers\BlockInspectionController::getValueToStatusMap($existingData->block_inspection_value_id) : null;
    $existingNotes = $existingData ? $existingData->comments : '';
@endphp

<input type="radio" ... {{ $selectedStatus === 'working' ? 'checked' : '' }}>
<textarea ...>{{ $existingNotes }}</textarea>
```

### 3. JavaScript Updates

**Refactored Color Application Logic:**
- Created `applyButtonColor()` function to centralize color logic
- Function checks if radio is checked and applies appropriate color
- Handles all status types and asset-specific colors

**Added Page Load Color Application:**
- On page load, find all checked radio buttons
- Apply colors immediately using `applyButtonColor()`
- Ensures pre-selected buttons show correct colors on load

**Code:**
```javascript
// Apply colors to pre-selected buttons on page load
document.querySelectorAll('.btn-check').forEach(function(radio) {
    if (radio.checked) {
        applyButtonColor(radio);
    }
});
```

## Files Modified

1. **Controller:** `app/Http/Controllers/BlockInspectionController.php`
   - Updated `edit()` method to load existing inspection assets
   - Added `getValueToStatusMap()` helper method

2. **View:** `resources/views/block-inspections/edit.blade.php`
   - Added PHP logic to determine pre-selected values
   - Updated radio buttons with `checked` attribute
   - Pre-filled textarea with existing notes
   - Refactored JavaScript for color application

## How It Works

### Data Flow on Edit Page Load

1. **Controller loads data:**
   ```
   BlockInspectionAsset → Filter by inspection_id and general_asset_id
   → Load relations (generalAsset, inspectionValue, images)
   → Key by general_asset_id → Pass to view
   ```

2. **View processes each asset:**
   ```
   For each general asset:
   → Check if existing data exists in collection
   → Map inspection_value_id to form status (working/not_working/na)
   → Extract notes
   → Render form with pre-selected values
   ```

3. **JavaScript applies colors:**
   ```
   On page load:
   → Find all checked radio buttons
   → For each checked button, apply appropriate color
   → Colors based on asset type and status
   ```

### Status Mapping

**Database → Form:**
- Good/Operational (IDs 2, 6) → 'working'
- Poor/Non-Operational (IDs 4, 8) → 'not_working'
- Fair/Pending (IDs 3, etc.) → 'na'

**Form → Database:**
- 'working' → Good/Operational
- 'not_working' → Poor/Non-Operational
- 'na' → Fair

## Color Mapping

- **Working/Clean/Good:** Green (#198754)
- **Not Working:** Red (#dc3545)
- **Average (Landscape/Building Externals):** Orange (#e67e22)
- **Poor (Landscape/Building Externals):** Red (#dc3545)
- **Not checked (Street Lights):** Orange (#e67e22)
- **N/A (Gates):** Green (#198754)

## Testing

1. Visit: https://proman.ddev.site/block-inspections/12/edit
2. Fill in general assets data:
   - Select status for each asset
   - Add notes
   - Upload photos (optional)
3. Click "Update Inspection"
4. Reopen the same page
5. ✅ Verify:
   - Radio buttons show previously selected status
   - Buttons have correct colors applied
   - Notes are pre-filled
   - All data retained

## Benefits

- ✅ User can see previously saved data
- ✅ No need to re-enter data on every edit
- ✅ Visual feedback with colored buttons
- ✅ Better UX and data integrity
- ✅ Consistent with form behavior standards

## Notes

- Photos are saved but not displayed in the form (future enhancement)
- Only general assets data is retained (building assets handled separately)
- Color application is automatic on page load
- Works with all 5 general assets (Gates, Street Lights, Landscape, Building Externals, etc.)

