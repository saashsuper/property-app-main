# Truncate and Seed Block Inspection Values - Quick Guide

## Overview
This approach **truncates** the `block_inspection_values` table and inserts 27 fresh records from production data (`saashmagna.sql`). This ensures clean, production-ready data.

## ⚠️ Important Warning
**This will delete all existing records** in the `block_inspection_values` table and replace them with the 27 production records.

---

## Quick Start (Recommended)

### Using the Automated Script

```bash
cd /Users/vijeesh/LaravelApps/property-app-main
./run-truncate-and-seed-block-values.sh
```

The script will:
- ✅ Prompt for confirmation
- ✅ Start DDEV if needed
- ✅ Run the migration
- ✅ Show verification results

---

## Alternative Methods

### Method 1: Laravel Migration

```bash
ddev exec php artisan migrate --path=database/migrations/2025_10_26_161000_truncate_and_seed_block_inspection_values.php
```

### Method 2: Direct SQL

```bash
ddev mysql < TRUNCATE_AND_INSERT_BLOCK_INSPECTION_VALUES.sql
```

### Method 3: Manual SQL

Connect to your database and run:
```sql
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE block_inspection_values;
-- Then insert all 27 records (see TRUNCATE_AND_INSERT_BLOCK_INSPECTION_VALUES.sql)
SET FOREIGN_KEY_CHECKS=1;
```

---

## What Gets Inserted

All 27 records from production with complete data:

| Field | Description |
|-------|-------------|
| `id` | IDs 1-27 (preserved from production) |
| `block_inspection_value_type_id` | Type IDs 1-9 |
| `name` | Value names (Yes, No, Working, etc.) |
| `color` | CSS classes for styling |
| `bg_color` | Background color identifiers |
| `created_at` | Original timestamps from production |
| `updated_at` | Original timestamps from production |

### Sample Records:
```
ID 1:  Yes (Type 1, Success/Green)
ID 2:  No (Type 1, Danger/Red)
ID 7:  Good (Type 3, Success/Green)
ID 10: Working (Type 4, Success/Green)
ID 13: Working (Type 5, Success/Green)
ID 16: Working (Type 6, Success/Green)
...and 21 more records
```

---

## Verification

After running, verify with:

```bash
# Check total count
ddev exec mysql -e "SELECT COUNT(*) FROM block_inspection_values;" db

# View all records
ddev exec mysql -e "SELECT id, name, color FROM block_inspection_values ORDER BY id;" db
```

Expected result: **27 records** with all `name` fields populated.

---

## Files Reference

| File | Purpose |
|------|---------|
| `run-truncate-and-seed-block-values.sh` | ⭐ **Use this** - Automated script |
| `database/migrations/2025_10_26_161000_truncate_and_seed_block_inspection_values.php` | Laravel migration |
| `TRUNCATE_AND_INSERT_BLOCK_INSPECTION_VALUES.sql` | Direct SQL script |
| `TRUNCATE_AND_SEED_GUIDE.md` | This guide |

---

## Safety Features

### Foreign Key Handling
The migration and SQL script temporarily disable foreign key checks:
```sql
SET FOREIGN_KEY_CHECKS=0;
-- Truncate and insert
SET FOREIGN_KEY_CHECKS=1;
```

This prevents errors if other tables reference `block_inspection_values`.

### Rollback
The migration includes a rollback that truncates the table:
```bash
ddev exec php artisan migrate:rollback --step=1
```

⚠️ **Note**: Rollback will delete all records without restoring old data.

---

## When to Use This Approach

✅ **Use truncate & seed when:**
- You want clean, production-ready data
- Starting fresh is preferred
- Existing data can be lost
- You need exact production IDs

❌ **Don't use if:**
- You have custom records not in production
- Other tables heavily reference these IDs with no FK cascade
- You need to preserve existing data

---

## Comparison with Update Approach

| Feature | Truncate & Seed | Update Only |
|---------|----------------|-------------|
| Removes old records | ✅ Yes | ❌ No |
| Clean slate | ✅ Yes | ❌ No |
| Preserves custom data | ❌ No | ✅ Yes |
| Production IDs | ✅ Exact | ⚠️ Depends |
| Execution time | Fast | Fast |
| Reversible | ⚠️ Partial | ✅ Yes |

---

## Troubleshooting

### Issue: Foreign key constraint fails
**Solution**: The migration handles this automatically by disabling FK checks.

### Issue: Permission denied
**Solution**: Make the script executable:
```bash
chmod +x run-truncate-and-seed-block-values.sh
```

### Issue: DDEV not running
**Solution**: The script will start DDEV automatically, or run:
```bash
ddev start
```

### Issue: Migration already exists
**Solution**: The migration is new and shouldn't conflict. Check:
```bash
ddev exec php artisan migrate:status
```

---

## Post-Update Testing

After running the truncate and seed:

1. **Test Block Inspection Edit**
   - Go to any block inspection edit page
   - Verify status dropdowns work
   - Check that values display correctly

2. **Test Block Inspection Show**
   - View any block inspection
   - Verify status badges display
   - Check colors are correct

3. **Test General Assets**
   - Edit a block inspection
   - Fill in general assets
   - Save and verify display

---

## Quick Reference

```bash
# Run the update (recommended)
./run-truncate-and-seed-block-values.sh

# Or using migration directly
ddev exec php artisan migrate --path=database/migrations/2025_10_26_161000_truncate_and_seed_block_inspection_values.php

# Verify results
ddev exec mysql -e "SELECT COUNT(*) FROM block_inspection_values;" db
```

---

## Summary

- ✅ **27 records** will be inserted
- ✅ **Clean production data** with correct IDs
- ✅ **All `name` fields** populated from production
- ✅ **Safe execution** with FK checks disabled
- ✅ **Automated script** for easy execution

**Recommended**: Use `run-truncate-and-seed-block-values.sh` for the safest, easiest execution.

