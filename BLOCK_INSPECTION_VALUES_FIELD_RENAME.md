# Block Inspection Values Field Rename: 'value' → 'name'

## Overview
This document outlines the change from the 'value' field to 'name' field in the `block_inspection_values` table.

## Changes Made

### 1. Migration Files

#### Original Structure
The table was originally created with a 'name' field:
- **File:** `database/migrations/2025_08_10_180052_create_block_inspection_values_table.php`
- Field: `name` (varchar 50)

#### Alignment Migration Updated
- **File:** `database/migrations/2025_10_26_111008_align_block_inspection_values_with_production.php`
- **Change:** Removed the column rename from 'name' to 'value'
- **Kept:** 
  - Drops 'description' column
  - Makes color columns NOT NULL

**Before:**
```php
// Renamed 'name' to 'value' to match production
Schema::table('block_inspection_values', function (Blueprint $table) {
    $table->renameColumn('name', 'value');
});
```

**After:**
```php
// Keeps column as 'name' (removed rename logic)
// Only drops description and modifies color columns
```

### 2. Model Update

**File:** `app/Models/BlockInspectionValue.php`

**Change:**
```php
protected $fillable = [
    'block_inspection_value_type_id',
    'name',  // Changed from 'value'
    'color',
    'bg_color',
];
```

### 3. Seeder Update

**File:** `database/seeders/BlockInspectionValueSeeder.php`

**Change:** All 27 records updated from 'value' => to 'name' =>

**Example:**
```php
// Before
['id' => 1, 'block_inspection_value_type_id' => 1, 'value' => 'Yes', ...]

// After
['id' => 1, 'block_inspection_value_type_id' => 1, 'name' => 'Yes', ...]
```

### 4. View Update

**File:** `resources/views/block-inspections/show.blade.php`

**Change:**
```blade
<!-- Before -->
{{ $asset->inspectionValue->value ?? 'N/A' }}

<!-- After -->
{{ $asset->inspectionValue->name ?? 'N/A' }}
```

## Database Schema

The `block_inspection_values` table now has:

| Column                          | Type           | Attributes    |
|--------------------------------|----------------|---------------|
| id                             | bigint unsigned| Primary, Auto |
| block_inspection_value_type_id | smallint unsigned | Foreign Key |
| name                           | varchar(50)    | NOT NULL      |
| color                          | varchar(50)    | NOT NULL      |
| bg_color                       | varchar(30)    | NOT NULL      |
| created_at                     | timestamp      | Nullable      |
| updated_at                     | timestamp      | Nullable      |

## Sample Data

All 27 inspection values now use the 'name' field:

| ID | Type ID | Name               | Color                  | BG Color  |
|----|---------|-------------------|------------------------|-----------|
| 1  | 1       | Yes               | btn-outline-success    | greens    |
| 2  | 1       | No                | btn-outline-danger     | reds      |
| 3  | 1       | Needs Attention   | btn-outline-primary    | oranges   |
| 4  | 2       | Clean             | btn-outline-success    | greens    |
| ...| ...     | ...               | ...                    | ...       |

## Migration Instructions

### For Fresh Installation
```bash
php artisan migrate:fresh --seed
```

### For Existing Database
If you've already run migrations with the old 'value' field:

```bash
# Option 1: Rollback and re-migrate
php artisan migrate:rollback --step=3
php artisan migrate
php artisan db:seed --class=BlockInspectionValueSeeder

# Option 2: Create a new migration to rename the column
php artisan make:migration rename_value_to_name_in_block_inspection_values
```

For Option 2, create migration content:
```php
public function up(): void
{
    Schema::table('block_inspection_values', function (Blueprint $table) {
        $table->renameColumn('value', 'name');
    });
}

public function down(): void
{
    Schema::table('block_inspection_values', function (Blueprint $table) {
        $table->renameColumn('name', 'value');
    });
}
```

## Files Modified

1. ✅ `database/migrations/2025_10_26_111008_align_block_inspection_values_with_production.php`
2. ✅ `app/Models/BlockInspectionValue.php`
3. ✅ `database/seeders/BlockInspectionValueSeeder.php`
4. ✅ `resources/views/block-inspections/show.blade.php`

## Testing Checklist

- [ ] Run fresh migration: `php artisan migrate:fresh --seed`
- [ ] Verify table structure: Check that column is named 'name' not 'value'
- [ ] Test inspection value display on block inspections show page
- [ ] Verify all 27 values are seeded correctly
- [ ] Check that inspection results display values properly

## Related Tables

- `block_inspection_value_types` - Parent table for value types
- `block_inspection_assets` - Uses block_inspection_value_id to reference values
- `building_assets` - Links to value types

## Notes

- The field is now consistently called 'name' throughout the codebase
- This aligns with Laravel naming conventions (e.g., similar to other models using 'name' field)
- No breaking changes to API if you're using model attributes (Eloquent handles field access)
- If you have any raw queries using 'value', they need to be updated to 'name'

