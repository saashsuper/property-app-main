# Archive Feature Analysis

## Overview
The archive feature in this Laravel property management application provides a smart deletion mechanism that preserves data integrity by archiving records with related entities instead of permanently deleting them. This ensures that historical data and relationships are maintained while allowing clean-up of unused records.

## Entities with Archive Functionality

The archive feature is implemented for the following entities:
1. **Blocks** (`Block` model)
2. **Block Issues** (`BlockIssue` model)
3. **Block Work Orders** (`BlockWorkOrder` model)
4. **Block Units** (`BlockUnit` model)

## Implementation Details

### 1. Database Schema

#### Block Work Orders
- **Migration**: `2026_01_10_063525_add_archive_status_to_block_work_orders_table.php`
- **Fields Added**:
  - `archive_status` (string, 20 chars, default: 'active') - Stores 'active' or 'archived'
  - `deleted_by` (mediumInteger, nullable) - Tracks which user archived the record

#### Block Issues
- **Migration**: `2026_01_10_061703_add_archive_status_to_block_issues_table.php`
- **Fields Added**:
  - `status` (string, 20 chars, default: 'active') - Used for archive status ('active' or 'archived')
  - `deleted_by` (mediumInteger, nullable) - Tracks which user archived the record

#### Blocks and Block Units
- Use existing `status` field (string) with values 'active' or 'archived'
- Both have `deleted_by` field for tracking

### 2. Model Implementation

All archive-enabled models share common patterns:

#### Status Constants
```php
const STATUS_ACTIVE = 'active';
const STATUS_ARCHIVED = 'archived';
```

#### Key Methods

**Archive Method** (`archive()`):
- Sets status to `STATUS_ARCHIVED`
- Records the archiving user in `deleted_by` field
- Performs soft delete (sets `deleted_at` timestamp)
- Returns boolean indicating success

**Check Methods**:
- `isArchived()`: Returns true if status is archived
- `isActive()`: Returns true if status is active and not soft deleted

**Query Scopes**:
- `scopeActive($query)`: Filters for active, non-deleted records
- `scopeArchived($query)`: Filters for archived records

**Relationship Check Methods**:
- Each model has methods to check for related entities:
  - `Block::hasRelatedEntities()` - Checks for units, buildings, issues, work orders, inspections, visits, contractors, information, or images
  - `BlockIssue::hasWorkOrders()` - Checks for related work orders
  - `BlockWorkOrder::hasBeenUpdated()` - Complex logic to determine if work order has been modified
  - `BlockUnit::hasIssues()` - Checks for related issues

### 3. Archive Logic by Entity

#### Blocks (`BlockController::destroy()`)
**Archive Condition**: Block has any related entities
- Units, buildings, issues, work orders, inspections, site visits, contractors, block information, or images

**Archive Action**:
- Deletes block image if exists
- Calls `$block->archive()` (sets status to 'archived', records deleter, soft deletes)

**Permanent Delete Action**:
- Only if block has NO related entities
- Deletes block image and all block images
- Performs `forceDelete()`

#### Block Issues (`BlockIssueController::destroy()`)
**Archive Condition**: Issue has related work orders

**Archive Action**:
- Calls `$blockIssue->archive()`

**Permanent Delete Action**:
- Only if issue has NO work orders
- Deletes all associated images
- Performs `forceDelete()`

#### Block Work Orders (`BlockWorkOrderController::destroy()`)
**Archive Condition**: Work order has been updated (determined by `hasBeenUpdated()` method)

**`hasBeenUpdated()` Logic**:
The method checks multiple conditions to determine if a work order has been modified:
1. `acceptance_status === 'accepted'` - Work order has been accepted
2. `status !== 1` - Status changed from assigned/pending (status 1)
3. Has notes (any note indicates update)
4. Has team members (indicates update)
5. Has logs excluding:
   - Creation logs
   - Attachment logs created within 10 seconds of work order creation
6. Has images uploaded after creation (excluding those within 10 seconds of creation)
7. `updated_at` differs from `created_at` by more than 5 seconds (accounts for database timing)

**Archive Action**:
- Calls `$blockWorkOrder->archive()`

**Permanent Delete Action**:
- Only if work order is still in assigned state with no updates
- Deletes all associated images
- Deletes PDF if exists
- Performs `forceDelete()`

#### Block Units (`BlockUnitController::destroy()`)
**Archive Condition**: Unit has related issues

**Archive Action**:
- Calls `$blockUnit->archive()`

**Permanent Delete Action**:
- Only if unit has NO issues
- Performs `forceDelete()`

### 4. User Interface

#### Visual Indicators
- **Archive Action**: Warning color (yellow/orange), archive icon (`ph-archive`)
- **Delete Action**: Danger color (red), trash icon (`ph-trash`)
- Button text dynamically changes: "Archive" vs "Delete"

#### Confirmation Modals
All delete/archive actions use confirmation modals that:
- Show different messages for archive vs delete
- Explain why the record will be archived (if applicable)
- Mention that archived records "can be restored later" (though restore functionality is not currently implemented)
- Display appropriate icons and colors

#### UI Locations
Archive/delete functionality appears in:
- Block show page (`blocks/show.blade.php`)
- Block issues tab (`blocks/tabs/issues.blade.php`)
- Block work orders tab (`blocks/tabs/edit/work-orders/index.blade.php`)
- Block units show page (`block-units/show.blade.php`)
- Block issues show page (`block-issues/show.blade.php`)
- Block work orders show page (`block-work-orders/show.blade.php`)
- Block work orders index page (`block-work-orders/index.blade.php`)

### 5. API Integration

The archive feature integrates with:
- AJAX/JSON requests - Controllers check for `request()->ajax()` or `request()->wantsJson()` and return JSON responses
- DataTables - Active scopes filter out archived records by default
- Notification system - Only active work orders are counted in notifications

### 6. Data Filtering

#### Active Records (Default)
- All queries use `scopeActive()` by default
- Filters out soft-deleted records (`deleted_at IS NULL`)
- Filters for active status (`status = 'active'` or `archive_status = 'active'`)

#### Archived Records
- Can be queried using `scopeArchived()`
- Requires explicitly including soft-deleted records with `withTrashed()`

### 7. Missing Functionality

#### Restore Feature
- **Status**: Not implemented
- **UI Messages**: Multiple views mention "can be restored later" but no restore functionality exists
- **Potential Implementation**: Would require:
  - Restore routes in `web.php`
  - Restore methods in controllers
  - Restore buttons in UI
  - Logic to set status back to 'active' and restore soft-deleted record

#### Archive Management
- No dedicated archive view/page to browse archived records
- No bulk archive/restore operations
- No archive filtering in list views

### 8. Key Features

#### Advantages
1. **Data Preservation**: Prevents accidental loss of important historical data
2. **Relationship Integrity**: Maintains foreign key relationships
3. **Audit Trail**: Tracks who archived records via `deleted_by` field
4. **Smart Logic**: Automatically determines archive vs delete based on data relationships
5. **User-Friendly**: Clear UI indicators and explanations

#### Considerations
1. **Soft Deletes**: Uses Laravel's SoftDeletes trait, so records remain in database
2. **Status Field**: Dual-purpose (business status + archive status) for some models
3. **Performance**: Relationship checks may impact performance on large datasets
4. **Restore Gap**: UI promises restore functionality that doesn't exist

### 9. Code Patterns

#### Archive Method Pattern
```php
public function archive(): bool
{
    $this->status = self::STATUS_ARCHIVED; // or archive_status for work orders
    $this->deleted_by = auth()->id();
    $this->save();
    return $this->delete(); // Soft delete
}
```

#### Controller Destroy Pattern
```php
public function destroy(Model $model)
{
    if ($model->hasRelatedEntities()) {
        $model->archive();
        return response()->json(['success' => true, 'message' => 'Archived message']);
    } else {
        // Delete related files
        $model->forceDelete();
        return response()->json(['success' => true, 'message' => 'Deleted message']);
    }
}
```

### 10. Testing

Test coverage exists for:
- Soft delete and restore functionality (mentioned in `TestSuiteDocumentation.md`)
- Block model tests include restore tests (`BlockModelTest.php`, `BlockControllerTest.php`)

## Recommendations

1. **Implement Restore Functionality**: Add restore routes, controller methods, and UI buttons
2. **Archive Management Page**: Create dedicated views for browsing and managing archived records
3. **Archive Filtering**: Add filters to list views to show/hide archived records
4. **Bulk Operations**: Consider bulk archive/restore capabilities
5. **Archive Reports**: Add reporting on archived records
6. **Performance Optimization**: Consider caching relationship checks for large datasets
7. **Documentation**: Update UI messages if restore won't be implemented, or implement it

## Files Modified/Created for Archive Feature

### Migrations
- `database/migrations/2026_01_10_061703_add_archive_status_to_block_issues_table.php`
- `database/migrations/2026_01_10_063525_add_archive_status_to_block_work_orders_table.php`

### Models
- `app/Models/Block.php`
- `app/Models/BlockIssue.php`
- `app/Models/BlockWorkOrder.php`
- `app/Models/BlockUnit.php`

### Controllers
- `app/Http/Controllers/BlockController.php`
- `app/Http/Controllers/BlockIssueController.php`
- `app/Http/Controllers/BlockWorkOrderController.php`
- `app/Http/Controllers/BlockUnitController.php`

### Views
- Multiple Blade templates updated with archive/delete UI logic
