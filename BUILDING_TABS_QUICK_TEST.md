# Building Tabs - Quick Testing Guide

## 🎯 Quick Test URL
**https://proman.ddev.site/block-inspections/61/edit**

---

## ⚡ 5-Minute Test

### ✅ Step 1: Page Load (1 min)
1. Open the URL above
2. **Check:**
   - [ ] Page loads without errors (F12 console)
   - [ ] See "General Information" accordion
   - [ ] See "General Assets" accordion
   - [ ] See building accordions (e.g., "BUILDING A", "BUILDING B") ← **NEW!**

### ✅ Step 2: Expand Building Tab (1 min)
1. Click on any building accordion to expand it
2. **Check:**
   - [ ] Accordion expands smoothly
   - [ ] See 6 assets listed:
     - Stairs
     - Lights
     - Lifts
     - Walls
     - Fire Alarm
     - Doors/Fire Doors
   - [ ] Each asset has:
     - Status buttons: [Working] [Not Working] [N/A]
     - Dropzone area (dashed border)
     - Notes textarea

### ✅ Step 3: Inspect One Asset (2 min)
1. For "Stairs" asset in any building:
   - [ ] Click "Working" button → turns **GREEN**
   - [ ] Drag 1-2 images into dropzone
   - [ ] Images preview appears with thumbnails
   - [ ] Add notes: "Test notes for stairs"

### ✅ Step 4: Submit Form (1 min)
1. Click "Update Inspection" button at bottom
2. **Check:**
   - [ ] Loading spinner appears
   - [ ] Button text changes to "Updating..."
   - [ ] Success notification appears
   - [ ] Redirect to inspections list

### ✅ Step 5: Verify Data Persisted (1 min)
1. Navigate back to: `/block-inspections/61/edit`
2. Expand the same building accordion
3. **Check:**
   - [ ] "Working" button still selected (green)
   - [ ] Notes still present
   - [ ] Uploaded images appear in "Existing Images" section
   - [ ] Can delete images by hovering and clicking X

---

## 🎨 Visual Reference

### Building Tab Structure
```
▶ BUILDING A                    ← Click to expand
  
  Stairs
  ├─ Status: [Working] [Not Working] [N/A]
  ├─ Photos: [Dropzone area]
  ├─ Existing: [img][img] (if any)
  └─ Notes: [Textarea]
  
  Lights
  ├─ Status: [Working] [Not Working] [N/A]
  ├─ Photos: [Dropzone area]
  └─ Notes: [Textarea]
  
  (... and 4 more assets)
```

---

## 🔍 What to Look For

### ✅ PASS Indicators
- Building names appear as accordion headers
- Each building has 6 assets (Stairs, Lights, Lifts, Walls, Fire Alarm, Doors/Fire Doors)
- Status buttons change color when clicked
- Dropzone accepts files
- Images preview correctly
- Form submits successfully
- Data persists on page reload

### ❌ FAIL Indicators
- No building accordions appear
- JavaScript errors in console
- Dropzone not accepting files
- Images not uploading
- Data not saving
- Page crashes on submit

---

## 🐛 Common Issues

### Issue: No building tabs appear
**Possible Cause:** Block has no associated buildings
**Check:**
```bash
ddev exec mysql -e "SELECT * FROM block_buildings WHERE block_id = (SELECT block_id FROM block_inspections WHERE id = 61);" db
```
**Solution:** Ensure the block has buildings in the database

### Issue: Dropzone not working
**Possible Cause:** JavaScript error
**Check:** Browser console (F12)
**Solution:** Refresh page, clear cache (Ctrl+Shift+R)

### Issue: Images not uploading
**Possible Cause:** File size or type
**Check:** Files are < 5MB and are images (JPG, PNG, etc.)
**Solution:** Use smaller image files

---

## 📊 Test Results Template

```
Test Date: __________
Tester: __________
Browser: __________

RESULTS:
- [ ] Page loads with building tabs
- [ ] Building tabs expand/collapse
- [ ] Each building shows 6 assets
- [ ] Status buttons work
- [ ] Dropzone accepts files
- [ ] Images preview correctly
- [ ] Notes field works
- [ ] Form submits successfully
- [ ] Data persists after save
- [ ] Existing images can be deleted

Overall: PASS / FAIL

Notes:
_________________________________
_________________________________
```

---

## 🚀 Ready to Test?

1. **Open URL:** https://proman.ddev.site/block-inspections/61/edit
2. **Open DevTools:** Press F12
3. **Follow Steps 1-5** above
4. **Mark Pass/Fail** for each step
5. **Report any issues** with screenshots

**Total Time:** 5-10 minutes

---

## 📚 Full Documentation

For detailed testing scenarios, see:
- `BUILDING_TABS_IMPLEMENTATION.md` - Complete implementation details
- `BLOCK_INSPECTION_EDIT_VERIFICATION_GUIDE.md` - General verification guide

---

## ✨ What's New?

**Before:** Only General Assets tab (Gates, Street Lights, Landscape, Building Externals)

**After:** 
- ✅ General Assets tab (unchanged)
- ✅ **NEW:** One tab per building (e.g., BUILDING A, BUILDING B, etc.)
- ✅ **NEW:** Each building tab has 6 specific assets to inspect
- ✅ **NEW:** Same features as General Assets (status, photos, notes)

---

**Happy Testing! 🎉**

