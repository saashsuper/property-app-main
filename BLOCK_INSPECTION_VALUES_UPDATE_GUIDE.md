# Block Inspection Values Update Guide

## Overview
This guide explains how to update the `block_inspection_values` table to copy data from the old `value` field to the new `name` field based on production data from `saashmagna.sql`.

## Background
The `block_inspection_values` table schema was updated to rename the `value` field to `name`. This update ensures that all existing records have their data properly migrated to the new field structure.

## Data to be Updated

The following 27 records will be updated:

| ID | Type ID | Name (from value field) | Color | BG Color |
|----|---------|------------------------|-------|----------|
| 1  | 1       | Yes                    | btn-outline-success | greens |
| 2  | 1       | No                     | btn-outline-danger | reds |
| 3  | 1       | Needs Attention        | btn-outline-primary | oranges |
| 4  | 2       | Clean                  | btn-outline-success | greens |
| 5  | 2       | Average                | btn-outline-primary | oranges |
| 6  | 2       | Poor                   | btn-outline-danger | reds |
| 7  | 3       | Good                   | btn-outline-success | greens |
| 8  | 3       | Average                | btn-outline-primary | oranges |
| 9  | 3       | Poor                   | btn-outline-danger | reds |
| 10 | 4       | Working                | btn-outline-success | greens |
| 11 | 4       | Not Working            | btn-outline-danger | reds |
| 12 | 4       | N/A                    | btn-outline-success | greens |
| 13 | 5       | Working                | btn-outline-success | greens |
| 14 | 5       | Not Working            | btn-outline-danger | reds |
| 15 | 5       | Not checked            | btn-outline-primary | oranges |
| 16 | 6       | Working                | btn-outline-success | greens |
| 17 | 6       | Partially Working      | btn-outline-primary | oranges |
| 18 | 6       | No lights              | btn-outline-danger | reds |
| 19 | 7       | Working                | btn-outline-success | greens |
| 20 | 7       | Not Working            | btn-outline-danger | reds |
| 21 | 7       | Needs Attention        | btn-outline-primary | oranges |
| 22 | 8       | No faults              | btn-outline-success | greens |
| 23 | 8       | Faults                 | btn-outline-danger | reds |
| 24 | 8       | Needs Attention        | btn-outline-primary | oranges |
| 25 | 9       | Working                | btn-outline-success | greens |
| 26 | 9       | Not Working            | btn-outline-danger | reds |
| 27 | 9       | No lights              | btn-outline-success | greens |

## Update Methods

### Method 1: Using Laravel Migration (Recommended)

Run the migration inside DDEV:

```bash
ddev exec php artisan migrate --path=database/migrations/2025_10_26_160000_update_block_inspection_values_copy_value_to_name.php
```

### Method 2: Using SQL Script

Run the SQL script directly:

```bash
# Via DDEV
ddev exec mysql -e "source /var/www/html/UPDATE_BLOCK_INSPECTION_VALUES.sql"

# Or import through DDEV's mysql command
ddev mysql < UPDATE_BLOCK_INSPECTION_VALUES.sql
```

### Method 3: Manual SQL Execution

Execute the SQL statements from `UPDATE_BLOCK_INSPECTION_VALUES.sql` in your database client:

1. Open your database management tool (phpMyAdmin, TablePlus, etc.)
2. Connect to the database
3. Run the SQL statements from `UPDATE_BLOCK_INSPECTION_VALUES.sql`

## Verification

After running the update, verify the changes:

```sql
SELECT id, block_inspection_value_type_id, name, color, bg_color 
FROM block_inspection_values 
ORDER BY id;
```

All records should now have their `name` field populated with the appropriate values.

## Files Created

1. **`database/migrations/2025_10_26_160000_update_block_inspection_values_copy_value_to_name.php`**
   - Laravel migration file
   - Can be run with `php artisan migrate`
   - Includes rollback functionality

2. **`UPDATE_BLOCK_INSPECTION_VALUES.sql`**
   - Direct SQL script
   - Can be executed in any MySQL client
   - Includes verification query

3. **`BLOCK_INSPECTION_VALUES_UPDATE_GUIDE.md`** (this file)
   - Comprehensive documentation
   - Multiple execution methods
   - Verification steps

## Related Changes

This update is part of the field rename from `value` to `name` in the `block_inspection_values` table. Related files that were updated:

- `app/Models/BlockInspectionValue.php` - Model using `name` field
- `app/Http/Controllers/BlockInspectionController.php` - Controller using `name` field
- `resources/views/block-inspections/show.blade.php` - View displaying `name` field
- Database seeders and migrations

## Troubleshooting

### Issue: Migration already ran
If you see "Nothing to migrate", the migration may have already been applied. Check:
```bash
ddev exec php artisan migrate:status
```

### Issue: Database connection error
Ensure DDEV is running:
```bash
ddev start
```

### Issue: Field doesn't exist
If the `name` field doesn't exist, you may need to run the schema migration first:
```bash
ddev exec php artisan migrate
```

## Important Notes

- This update is **idempotent** - it can be run multiple times safely
- Existing `name` field values will be overwritten
- The `updated_at` timestamp will be updated to the current time
- No data is deleted, only updated
- A rollback option is available in the migration file

## Next Steps

After updating the data:

1. ✅ Verify all records have `name` field populated
2. ✅ Test the block inspection edit functionality
3. ✅ Test the block inspection show page
4. ✅ Verify general assets are displaying correctly
5. ✅ Check that status badges show properly with the new `name` field

## Support

If you encounter any issues during the update process, please check:
- Database connection settings in `.env` or `env.ddev`
- DDEV container status
- Migration table for duplicate entries
- Laravel logs in `storage/logs/laravel.log`

