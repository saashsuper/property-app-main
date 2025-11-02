# 🚀 Quick Start - Building Features Testing

## ⚡ 3-STEP SETUP

### Step 1: Run Migration & Seeder
```bash
ddev start
ddev exec php artisan migrate
ddev exec php artisan db:seed --class=BlockBuildingTypeAssetSeeder
```

### Step 2: Verify Data
```bash
ddev exec mysql -e "SELECT COUNT(*) FROM block_building_type_assets;" db
```
**Expected:** 11 records

### Step 3: Open Test Page
```
https://proman.ddev.site/block-inspections/61/edit
```

---

## ✅ WHAT TO VERIFY

### Test 1: Duplex Building Shows ONLY Stairs (1 minute)
1. Open the edit page
2. Find a building with "[Duplex]" badge
3. Expand its accordion
4. ✅ **Should show ONLY 1 asset: Stairs**
5. ✅ Stairs should have: [Clean] [Average] [Poor]
6. ✅ Should NOT show: Lights, Lifts, Walls, Fire Alarm, Doors

### Test 2: High Rise Building Shows All 6 Assets (1 minute)
1. Find a building with "[High Rise]" badge
2. Expand its accordion
3. ✅ **Should show 6 assets:**
   - Stairs
   - Lights
   - Lifts
   - Walls
   - Fire Alarm
   - Doors/Fire Doors
4. ✅ Each asset has different inspection values

### Test 3: Submit Form (2 minutes)
1. Select "Average" for Stairs in Duplex building
2. Upload 1 image
3. Add note: "Test note"
4. Click "Update Inspection"
5. ✅ Should save successfully
6. ✅ Reload page → data persists

---

## 🎨 Expected Display

### Duplex Building:
```
▶ BUILDING B  [Duplex]

  Stairs
  ├─ Status: [Clean] [Average] [Poor]
  ├─ Photos: [Dropzone]
  └─ Notes: [Textarea]
  
  (ONLY 1 asset shown ✅)
```

### High Rise Building:
```
▶ BUILDING A  [High Rise]

  Stairs → [Clean] [Average] [Poor]
  Lights → [Working] [Not Working] [N/A]
  Lifts → [Working] [Not Working] [Needs Attention]
  Walls → [Good] [Average] [Poor]
  Fire Alarm → [No faults] [Faults] [Needs Attention]
  Doors → [Working] [Not Working] [N/A]
  
  (6 assets shown ✅)
```

---

## 📊 Quick Verification Query

```bash
# Check Duplex assets (should return only Stairs - asset 5)
ddev exec mysql -e "
SELECT bba.name 
FROM block_building_type_assets bbta
JOIN block_building_assets bba ON bba.id = bbta.block_building_asset_id
WHERE bbta.block_building_type_id = 2
  AND bbta.block_building_asset_id >= 5;
" db
```

**Expected Output:**
```
+--------+
| name   |
+--------+
| Stairs |
+--------+
1 row
```

---

## ✅ Success Checklist

- [ ] Migration run successfully
- [ ] Seeder run successfully (11 records)
- [ ] Page loads without errors
- [ ] Building type badges visible in accordion headers
- [ ] Duplex building shows ONLY Stairs
- [ ] High Rise building shows 6 assets
- [ ] Each asset shows correct inspection values
- [ ] Can select values (buttons turn green/orange/red)
- [ ] Can upload images via dropzone
- [ ] Form submits successfully
- [ ] Data persists after save

---

## 🐛 Quick Troubleshooting

### No buildings showing?
```bash
# Check if block has buildings
ddev exec mysql -e "SELECT * FROM block_buildings WHERE block_id = (SELECT block_id FROM block_inspections WHERE id = 61);" db
```

### All buildings show same assets?
```bash
# Re-run seeder
ddev exec php artisan db:seed --class=BlockBuildingTypeAssetSeeder
```

### Page crashes?
- Check browser console (F12)
- Check Laravel logs: `storage/logs/laravel.log`

---

## 📚 Full Documentation

For detailed information, see:
- `SESSION_SUMMARY_BUILDING_FEATURES.md` - Complete session summary
- `BUILDING_TYPE_BASED_ASSET_FILTERING.md` - Filtering implementation
- `BUILDING_TYPE_FILTERING_SUMMARY.md` - Quick reference

---

## 🎯 CRITICAL TEST

**The most important test:**

> **Find a Duplex building → Should show ONLY Stairs**

If this works, everything is correct! ✅

---

**Total Setup Time:** ~5 minutes  
**Total Test Time:** ~5 minutes  
**Total:** ~10 minutes to full verification

**Ready? Start DDEV and test! 🚀**

