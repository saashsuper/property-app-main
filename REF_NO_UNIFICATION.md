# Reference Number (ref_no) Unification Documentation

## Overview
This document describes the unification of reference number (ref_no) generation for issues in the property management application. The new format provides a consistent, date-based reference system.

## Current Implementation Analysis

### Previous Formats
1. **BlockIssue**: `YYYY-MM-XXX` format (e.g., `2025-10-001`)
2. **Issue**: `YYYY-MM-XXX` format (e.g., `2025-10-001`)
3. **BlockWorkOrder**: `WO-YYYYMM-XXX` format (e.g., `WO-202510-001`)
4. **BlockVisit**: `SV-XXXXXX` format (random 6 characters)
5. **BlockInspection**: `BI-XXXX` format (4 digits)

## New Unified Format

### Format Specification
- **Format**: `YYMM` + `ID`
- **Example**: For January 2026 with ID 1 → `26011`
- **Example**: For January 2026 with ID 123 → `2601123`

### Implementation Scope
- ✅ **BlockIssue**: Implemented in `BlockIssue::generateRefNo()`
- ✅ **Issue**: Implemented in `Issue::generateRefNo()` (model boot method)
- ✅ **BlockWorkOrder**: Implemented in `BlockWorkOrder::generateRefNo()` (model boot method)
- ✅ **BlockVisit**: Implemented in `BlockVisit::generateRefNo()` (model boot method)
- ✅ **BlockInspection**: Implemented in `BlockInspection::generateRefNo()` (model boot method)

## Technical Details

### Generation Logic
1. Extract current year (2 digits) and month (2 digits) → `YYMM`
2. Use the database record ID after creation → `ID`
3. Concatenate: `YYMM` + `ID`

### Implementation Notes
- **No database migration required**: Uses existing `ref_no` field
- **No existing data modification**: Only affects newly created issues
- **Backward compatibility**: Existing ref_no values remain unchanged
- **Generation timing**: Ref_no is generated after record creation (using `created` event) to access the ID

### Code Locations
- `app/Models/BlockIssue.php` - `generateRefNo()` method (called in `created` event)
- `app/Models/Issue.php` - `generateRefNo()` method (called in `created` event)
- `app/Models/BlockWorkOrder.php` - `generateRefNo()` method (called in `created` event)
- `app/Models/BlockVisit.php` - `generateRefNo()` method (called in `created` event)
- `app/Models/BlockInspection.php` - `generateRefNo()` method (called in `created` event)

## Display Locations
The ref_no is displayed in various views:
- Issue listing pages
- Issue detail/show pages
- Block issue pages
- Work order pages (showing related issue ref_no)
- Site visit pages
- Inspection pages
- API responses
- Export files
- Notifications and alerts

## Examples

### New Issue Creation
- Created: January 26, 2026
- Database ID: 1
- Generated ref_no: `26011`

### Another Example
- Created: January 26, 2026
- Database ID: 456
- Generated ref_no: `2601456`

## Benefits
1. **Consistency**: Unified format across issue types
2. **Date Context**: YYMM prefix provides temporal context
3. **Uniqueness**: ID ensures uniqueness
4. **Simplicity**: Shorter, more readable format
5. **No Migration**: No database changes required

## Migration Notes
- Existing ref_no values are preserved
- Only new issues created after implementation will use the new format
- Search functionality continues to work with both old and new formats

## Implementation Complete
All entities (Issues, BlockIssues, Work Orders, Visits, and Inspections) now use the unified YYMM + ID format for ref_no generation.

## Future Considerations
- Monitor for any conflicts or issues with the new format
- Consider adding validation to ensure ref_no uniqueness if not already enforced
- Update any tests that rely on the old ref_no format
