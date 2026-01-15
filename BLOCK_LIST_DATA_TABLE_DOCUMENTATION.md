# Block List Data Table Feature Documentation

## Overview

The Block List Data Table is a feature that displays all active blocks in a property management system. It provides a comprehensive view of blocks with sorting, searching, pagination, and export capabilities. The table is implemented using DataTables.js library with client-side processing.

**URL:** `https://proman.ddev.site:8443/blocks`

## Table of Contents

1. [Route & Controller](#route--controller)
2. [View Structure](#view-structure)
3. [Data Table Configuration](#data-table-configuration)
4. [Columns & Data Display](#columns--data-display)
5. [Features](#features)
6. [Data Relationships](#data-relationships)
7. [Access Control](#access-control)
8. [Export Functionality](#export-functionality)
9. [Technical Implementation](#technical-implementation)

---

## Route & Controller

### Route Definition

**File:** `routes/web.php`

```php
Route::get('blocks', [App\Http\Controllers\BlockController::class, 'index'])->name('blocks.index');
```

- **Method:** GET
- **Middleware:** `auth` (authentication required)
- **Route Name:** `blocks.index`

### Controller Method

**File:** `app/Http/Controllers/BlockController.php`

```php
public function index(Request $request)
{
    $query = Block::with(['blockType', 'user', 'creator', 'units', 'blockManager', 'issues', 'workOrders'])->active();
    $blocks = $query->orderBy('created_at', 'desc')->get();
    return view('blocks.index', compact('blocks'));
}
```

**Key Points:**
- Eager loads relationships: `blockType`, `user`, `creator`, `units`, `blockManager`, `issues`, `workOrders`
- Uses `active()` scope to filter only active blocks (not soft-deleted and status = 'active')
- Orders by `created_at` descending (newest first)
- Returns all blocks (no pagination at controller level - handled by DataTables)

---

## View Structure

### Main View File

**File:** `resources/views/blocks/index.blade.php`

**Layout:** Extends `layouts.master`

**Sections:**
- `@section('title')` - Page title: "Blocks"
- `@section('css')` - DataTable CSS and custom column width styles
- `@section('content')` - Main table content
- `@section('script')` - DataTable initialization JavaScript

### Components Used

1. **`<x-datatable-base />`** - Base DataTable CSS and dependencies
2. **`<x-datatable-loader />`** - Loading spinner component
3. **`<x-datatable-scripts />`** - DataTable JavaScript library

---

## Data Table Configuration

### JavaScript Initialization

**Location:** `resources/views/blocks/index.blade.php` (lines 168-263)

```javascript
$('#blocks-table').DataTable({
    responsive: true,
    scrollX: false,
    autoWidth: false,
    processing: true,
    dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex align-items-center"i><"d-flex align-items-center"p>>',
    order: [[0, 'asc']], // Default sort by Name column
    columnDefs: [
        { targets: [4, 5, 6], type: 'num' }, // Units, Issues, Work Orders - numeric sorting
        { targets: [0], width: '25%' }, // Name
        { targets: [1], width: '20%' }, // Management Company
        { targets: [2], width: '15%' }, // Block Manager
        { targets: [3], width: '25%' }, // Address
        { targets: [4], width: '10%' },  // Units
        { targets: [5], width: '10%' },  // Issues
        { targets: [6], width: '15%' }   // Work Orders
    ],
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    language: {
        search: "Search blocks:",
        lengthMenu: "Show _MENU_ blocks per page",
        info: "Showing _START_ to _END_ of _TOTAL_ blocks",
        infoEmpty: "Showing 0 to 0 of 0 blocks",
        infoFiltered: "(filtered from _MAX_ total blocks)",
        paginate: { first: "First", last: "Last", next: "Next", previous: "Previous" },
        processing: '<i class="fas fa-spinner fa-spin fa-2x"></i><br>Loading...'
    }
});
```

### Configuration Details

| Option | Value | Description |
|--------|-------|-------------|
| `responsive` | `true` | Enables responsive table behavior |
| `scrollX` | `false` | Disables horizontal scrolling |
| `autoWidth` | `false` | Uses defined column widths |
| `processing` | `true` | Shows processing indicator during operations |
| `pageLength` | `10` | Default rows per page |
| `lengthMenu` | `[10, 25, 50, -1]` | Page length options (All = -1) |
| `order` | `[[0, 'asc']]` | Default sort: Name column ascending |

---

## Columns & Data Display

The table displays 7 columns:

### 1. Name (Column 0)
- **Width:** 25%
- **Content:**
  - Block name as clickable link to `blocks.show` route
  - Block type badge (R, C, R+C, or first letter)
  - Badge colors: `bg-primary`
- **Sorting:** Text-based (default sort column)

### 2. Management Company (Column 1)
- **Width:** 20%
- **Content:** `$block->management_company`
- **Styling:** Truncated with tooltip on hover (`table-cell-truncate` class)

### 3. Block Manager (Column 2)
- **Width:** 15%
- **Content:** `$block->blockManager->name ?? 'N/A'`
- **Relationship:** `blockManager` (User model via `block_manager_id`)
- **Styling:** Truncated with tooltip

### 4. Address (Column 3)
- **Width:** 25%
- **Content:** `$block->block_address` (mapped from `address1` field)
- **Styling:** Truncated with tooltip

### 5. Units (Column 4)
- **Width:** 10%
- **Content:** Count of related units (`$block->units->count()`)
- **Display:** Badge with `bg-info` class
- **Sorting:** Numeric type

### 6. Issues (Column 5)
- **Width:** 10%
- **Content:** Count of related issues (`$block->issues->count()`)
- **Display:** 
  - If count > 0: Clickable badge (`bg-warning`) linking to `block-issues.index` with `block_id` filter
  - If count = 0: Non-clickable badge (`bg-secondary`)
- **Sorting:** Numeric type

### 7. Work Orders (Column 6)
- **Width:** 15%
- **Content:** Count of active work orders (`$block->workOrders->where('status', 1)->count()`)
- **Display:** Badge with `bg-success` class
- **Sorting:** Numeric type
- **Note:** Only counts work orders with `status = 1` (active)

---

## Features

### 1. Sorting
- **All columns are sortable** (indicated by up/down arrow icons)
- Default sort: Name column (ascending)
- Numeric columns (Units, Issues, Work Orders) use numeric sorting
- Text columns use alphabetical sorting

### 2. Searching
- **Global search** across all columns
- Search box label: "Search blocks:"
- Real-time filtering as user types
- Case-insensitive search

### 3. Pagination
- **Default:** 10 rows per page
- **Options:** 10, 25, 50, All
- Pagination controls at bottom of table
- Shows: "Showing X to Y of Z blocks"

### 4. Responsive Design
- Table adapts to screen size
- Column widths are percentage-based
- Text truncation with tooltips for long content

### 5. Loading State
- Loading spinner displayed during initialization
- Processing indicator during sort/search operations
- Fallback timeout (3 seconds) to force table display if initialization fails

---

## Data Relationships

### Eager Loaded Relationships

The controller eager loads the following relationships for performance:

1. **`blockType`** - BlockType model (belongsTo)
   - Used for: Block type badge display

2. **`user`** - User model (belongsTo, `user_id`)
   - Original user relationship (may be legacy)

3. **`creator`** - User model (belongsTo, `created_by`)
   - User who created the block

4. **`units`** - BlockUnit collection (hasMany)
   - Used for: Units count display

5. **`blockManager`** - User model (belongsTo, `block_manager_id`)
   - Used for: Block Manager column display

6. **`issues`** - BlockIssue collection (hasMany)
   - Used for: Issues count display

7. **`workOrders`** - BlockWorkOrder collection (hasMany)
   - Used for: Work Orders count display

### Block Model Scope

**Active Scope:**
```php
public function scopeActive($query)
{
    return $query->whereNull('deleted_at')
                ->where('status', self::STATUS_ACTIVE);
}
```

Only blocks that are:
- Not soft-deleted (`deleted_at IS NULL`)
- Have status = 'active'

are displayed in the table.

---

## Access Control

### Route Protection

- **Authentication Required:** All users must be logged in (`auth` middleware)
- **View Access:** All authenticated users can view the blocks list
- **Create Access:** Only Admin role can create blocks
  ```php
  @admin
  <a href="{{ route('blocks.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-2"></i>Add New Block
  </a>
  @endadmin
  ```

### Role-Based Actions

- **Admin/Super Admin/Manager/Viewer:** Can create, edit, delete blocks
- **Other Roles:** Can only view blocks

---

## Export Functionality

### Export Options

Located in the table header, three export buttons are available:

1. **PDF Export**
   - Route: `export.pdf` with type `blocks`
   - Icon: `ph-file-pdf`
   - Class: `btn-outline-danger`

2. **Excel Export**
   - Route: `export.excel` with type `blocks`
   - Icon: `ph-file-xls`
   - Class: `btn-outline-success`

3. **Print**
   - Route: `export.print` with type `blocks`
   - Icon: `ph-printer`
   - Opens in new window (`target="_blank"`)

**Implementation:**
```blade
<a href="{{ route('export.pdf', 'blocks') }}?{{ http_build_query(request()->query()) }}" 
   class="btn btn-outline-danger btn-sm" title="Export to PDF">
    <i class="ph-file-pdf"></i>
</a>
```

All export routes preserve current query parameters (filters, search, etc.).

---

## Technical Implementation

### CSS Styling

**Column Width Management:**
```css
#blocks-table th:nth-child(1) { width: 25%; } /* Name */
#blocks-table th:nth-child(2) { width: 20%; } /* Management Company */
#blocks-table th:nth-child(3) { width: 15%; } /* Block Manager */
#blocks-table th:nth-child(4) { width: 25%; } /* Address */
#blocks-table th:nth-child(5) { width: 10%; }  /* Units */
#blocks-table th:nth-child(6) { width: 10%; }  /* Issues */
#blocks-table th:nth-child(7) { width: 15%; } /* Work Orders */
```

### DOM Structure

**DataTables DOM String:**
```
<"d-flex justify-content-between align-items-center mb-3"
  <"d-flex align-items-center"l>  // Length menu
  <"d-flex align-items-center"f>  // Filter/Search
>
rt  // Table body
<"d-flex justify-content-between align-items-center mt-3"
  <"d-flex align-items-center"i>  // Info
  <"d-flex align-items-center"p>  // Pagination
>
```

### Error Handling

1. **DataTables Library Check:**
   ```javascript
   if (!$.fn.DataTable) {
       console.error('DataTables library not loaded');
       $('#blocks-table-loading').addClass('d-none');
       $('#blocks-table').show();
       return;
   }
   ```

2. **Try-Catch Block:**
   - Wraps DataTable initialization
   - Logs errors to console
   - Falls back to showing table without DataTables

3. **Timeout Fallback:**
   - 3-second timeout to force table display if initialization hangs
   - Prevents indefinite loading state

### Empty State

When no blocks are found:
```blade
<tr>
    <td colspan="7" class="text-center py-4">
        <div class="text-muted">
            <i class="fas fa-inbox fa-3x mb-3"></i>
            <p>No blocks found. 
                @admin
                <a href="{{ route('blocks.create') }}" class="text-primary">Create your first block</a>
                @else
                Contact an administrator to create blocks.
                @endadmin
            </p>
        </div>
    </td>
</tr>
```

---

## Data Flow

1. **User Request:** User navigates to `/blocks`
2. **Route:** `blocks.index` route is matched
3. **Controller:** `BlockController@index` method executes
4. **Query:** Fetches active blocks with eager-loaded relationships
5. **View:** Renders `blocks.index` blade template
6. **Initialization:** DataTables JavaScript initializes on page load
7. **Display:** Table is shown with loading spinner hidden
8. **Interaction:** User can sort, search, paginate, and export

---

## Performance Considerations

### Optimizations

1. **Eager Loading:** All relationships are eager loaded to prevent N+1 queries
2. **Client-Side Processing:** DataTables handles sorting/searching on client side (suitable for moderate datasets)
3. **Column Widths:** Fixed widths prevent layout shifts

### Potential Improvements

1. **Server-Side Processing:** For large datasets (>1000 blocks), consider server-side DataTables
2. **Caching:** Cache block counts (units, issues, work orders) if they don't change frequently
3. **Lazy Loading:** Load relationship counts on demand for better initial page load

---

## Related Files

### Backend
- `app/Http/Controllers/BlockController.php` - Main controller
- `app/Models/Block.php` - Block model with relationships
- `routes/web.php` - Route definitions

### Frontend
- `resources/views/blocks/index.blade.php` - Main view
- `resources/views/components/datatable-base.blade.php` - DataTable CSS
- `resources/views/components/datatable-scripts.blade.php` - DataTable JS
- `resources/views/components/datatable-loader.blade.php` - Loading component

### Related Models
- `app/Models/BlockType.php` - Block types
- `app/Models/BlockUnit.php` - Units
- `app/Models/BlockIssue.php` - Issues
- `app/Models/BlockWorkOrder.php` - Work orders
- `app/Models/User.php` - Users (block managers)

---

## Summary

The Block List Data Table is a feature-rich, client-side processed table that displays active blocks with:
- ✅ 7 columns showing key block information
- ✅ Full sorting and searching capabilities
- ✅ Pagination with customizable page sizes
- ✅ Export to PDF, Excel, and Print
- ✅ Responsive design
- ✅ Role-based access control
- ✅ Optimized with eager loading
- ✅ Error handling and fallbacks

The implementation uses DataTables.js for enhanced user experience and follows Laravel best practices for data fetching and display.
