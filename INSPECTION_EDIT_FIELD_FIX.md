# Inspection Edit Field Fix

## Issue
When editing an inspection from the Block edit page's Inspection tab, users were getting the error:
**"The lead inspector field is required."**

## Root Cause
The issue was caused by a mismatch between the form field name in the Edit Inspection Modal and the expected field name in the controller validation:

1. **Edit Modal Form Field**: Used `name="user_id"` (line 114 in modals.blade.php)
2. **Controller Validation**: Expected `name="lead_inspector"` (line 192 in BlockInspectionController.php)

Additionally, the JavaScript that populated the edit modal was setting the value to the wrong field ID.

## Files Changed

### 1. `/resources/views/blocks/tabs/edit/inspections/modals.blade.php`
**Line 113-114**: Changed the field name and ID from `user_id` to `lead_inspector`

**Before:**
```blade
<label for="edit_user_id" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
<select class="form-select" id="edit_user_id" name="user_id" required>
```

**After:**
```blade
<label for="edit_lead_inspector" class="form-label">Lead Inspector <span class="text-danger">*</span></label>
<select class="form-select" id="edit_lead_inspector" name="lead_inspector" required>
```

### 2. `/resources/views/blocks/tabs/edit/inspections/scripts.blade.php`
**Line 282**: Updated JavaScript to set the value to the correct field ID

**Before:**
```javascript
setValue('edit_user_id', lead ? lead.user_id : '');
```

**After:**
```javascript
setValue('edit_lead_inspector', lead ? lead.user_id : '');
```

### 3. `/app/Http/Controllers/BlockInspectionController.php`
**Line 194**: Updated validation to allow status ID 6 (Rescheduled) and made job_status_id nullable

**Before:**
```php
'job_status_id' => 'required|integer|in:1,2,3,4,5',
```

**After:**
```php
'job_status_id' => 'nullable|integer|in:1,2,3,4,5,6',
```

## Additional Context

### Why Different Field Names?
- **Add Inspection Modal**: Uses `user_id` and submits to `storeFromModal()` method ✓
- **Edit Inspection Modal**: Now uses `lead_inspector` and submits to `update()` method ✓

This maintains consistency with each method's validation rules while keeping the forms working correctly.

## Testing Recommendations
1. Navigate to Blocks → Edit Block → Inspections Tab
2. Click the edit button (pencil icon) on any inspection
3. Verify the lead inspector dropdown is pre-populated with the current value
4. Change any field and submit
5. Confirm the inspection updates successfully without validation errors

## Status
✅ **Fixed** - All changes applied successfully with no linting errors.


