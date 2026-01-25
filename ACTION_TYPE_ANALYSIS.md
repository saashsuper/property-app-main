# Action Type Dropdown Values - Analysis & Documentation

## Overview
This document analyzes the Action Type dropdown values used in the Block Issue Actions feature.

## Current State

### 1. Model Definition (`app/Models/BlockIssueAction.php`)
The model defines `ACTION_TYPES` constant with **8 values**:

```php
const ACTION_TYPES = [
    'site_visit' => 'Site Visit',
    'phone_contact' => 'Phone Contact',
    'email' => 'Email',
    'letter' => 'Letter',
    'in_person_visit' => 'In person visit',
    'clean_up' => 'Clean up',
    'minor_repairs' => 'Minor repairs',
    'other' => 'Other'
];
```

### 2. View Files

#### `resources/views/block-issues/edit.blade.php` ✅
- **Status**: Correct
- **Implementation**: Uses model constant dynamically
```blade
@foreach (\App\Models\BlockIssueAction::ACTION_TYPES as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
@endforeach
```

#### `resources/views/block-issues/show.blade.php` ✅ (Fixed)
- **Status**: Fixed to use model constant
- **Previous Issue**: Had hardcoded values that didn't match the model:
  - inspection ✅
  - maintenance ✅
  - repair ✅
  - replacement ❌ (not in model)
  - cleaning ❌ (not in model)
  - other ❌ (not in model)

### 3. Database Structure
- **Table**: `block_issue_actions`
- **Column**: `action_type` (VARCHAR, max 50 characters)
- **Type**: String field (not an enum, not a foreign key)
- **Note**: No separate lookup table exists for action types

### 4. Controller Validation (`app/Http/Controllers/BlockIssueController.php`)
- **Current**: Accepts any string up to 50 characters
- **Validation Rule**: `'action_type' => 'required|string|max:50'`
- **Issue**: No validation against `ACTION_TYPES` constant

## Issues Identified

### ✅ Fixed
1. **Inconsistent dropdown values**: `show.blade.php` had hardcoded values that didn't match the model constant
   - **Solution**: Updated to use model constant like `edit.blade.php`

### ⚠️ Recommendations

1. **Add validation in controller** to ensure only valid action types are accepted:
   ```php
   'action_type' => ['required', 'string', Rule::in(array_keys(BlockIssueAction::ACTION_TYPES))],
   ```

2. **Consider adding missing values** if 'replacement', 'cleaning', or 'other' are needed:
   - If these values are required, add them to the `ACTION_TYPES` constant in the model
   - Otherwise, ensure all views use the model constant

3. **Database Seeding**: Since `action_type` is a string field (not a separate table), no seeder is needed. However, if you want to ensure data consistency:
   - Add validation in the controller (recommended above)
   - Consider creating a migration to add a check constraint (if database supports it)

## Action Type Values Summary

| Key | Display Name | Status |
|-----|--------------|--------|
| site_visit | Site Visit | ✅ Active |
| phone_contact | Phone Contact | ✅ Active |
| email | Email | ✅ Active |
| letter | Letter | ✅ Active |
| in_person_visit | In person visit | ✅ Active |
| clean_up | Clean up | ✅ Active |
| minor_repairs | Minor repairs | ✅ Active |
| other | Other | ✅ Active |

## Testing
- Tests in `tests/Feature/BlockIssuePageTest.php` use:
  - `'inspection'` ⚠️ (needs update - no longer valid)
  - `'repair'` ⚠️ (needs update - no longer valid)
  - **Note**: Tests should be updated to use new action type values

## Next Steps

1. ✅ **Fixed**: Updated `show.blade.php` to use model constant
2. ⚠️ **Recommended**: Add validation in controller to restrict action_type to valid values
3. ⚠️ **Optional**: If 'replacement', 'cleaning', or 'other' are needed, add them to the model constant

## Notes
- The `action_type` field is stored as a plain string in the database
- No separate lookup table exists for action types
- All dropdowns should reference `BlockIssueAction::ACTION_TYPES` for consistency
- The model constant serves as the single source of truth for valid action types
