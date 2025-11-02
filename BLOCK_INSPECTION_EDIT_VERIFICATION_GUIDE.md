# Block Inspection Edit Page - Feature Verification Guide

## URL to Test
**https://proman.ddev.site/block-inspections/61/edit**

---

## 🎯 FEATURE VERIFICATION CHECKLIST

### ✅ SECTION 1: PAGE LOAD & LAYOUT

#### 1.1 Page Structure
- [ ] Page loads without errors (check browser console)
- [ ] Breadcrumb navigation displays correctly: Blocks > Block Inspections > {Ref No} > Edit Inspection
- [ ] Card header shows "Edit Block Inspection"
- [ ] Two accordion sections are visible:
  - General Information (expanded by default)
  - General Assets (collapsed by default)
- [ ] Form action buttons visible at bottom: Cancel & Update Inspection

#### 1.2 Browser Console Check
Open browser DevTools (F12) and check:
- [ ] No JavaScript errors
- [ ] Dropzone library loaded (check Network tab for `dropzone`)
- [ ] Flatpickr library loaded (for date picker)

---

### ✅ SECTION 2: GENERAL INFORMATION ACCORDION

#### 2.1 Form Fields Display
- [ ] **Block** dropdown:
  - Shows current block selected
  - Can change to other blocks
  - Shows format: "Block Name - Address"
  - Required field (red asterisk)

- [ ] **Lead Inspector** dropdown:
  - Shows current lead inspector
  - Lists only Property Manager users
  - Shows format: "Name (email)"
  - Required field (red asterisk)

- [ ] **Status** dropdown:
  - Shows current status
  - Options available: Scheduled, In Progress, Completed, Cancelled, On Hold
  - Required field (red asterisk)

#### 2.2 Date & Time Fields
- [ ] **Scheduled Date**:
  - Shows current scheduled date
  - Calendar icon visible
  - Opens date picker on click
  - Format: dd/mm/yyyy
  - Required field

- [ ] **Scheduled Time**:
  - Shows current scheduled time
  - Clock icon visible
  - Time picker opens
  - Format: HH:mm (24-hour)
  - Required field

- [ ] **Start Date**:
  - Shows start date if exists
  - Calendar icon visible
  - Opens date picker on click
  - Optional field

- [ ] **Start Time**:
  - Shows start time if exists
  - Clock icon visible
  - Time picker opens
  - Optional field

- [ ] **End Date**:
  - Shows end date if exists
  - Calendar icon visible
  - Opens date picker on click
  - Optional field

- [ ] **End Time**:
  - Shows end time if exists
  - Clock icon visible
  - Time picker opens
  - Optional field

- [ ] **Note**:
  - Shows existing notes
  - Textarea (3 rows)
  - Placeholder text visible
  - Optional field

---

### ✅ SECTION 3: GENERAL ASSETS ACCORDION

#### 3.1 Accordion Behavior
- [ ] Click "GENERAL ASSETS" header to expand
- [ ] Accordion smoothly expands/collapses
- [ ] Icon changes on expand/collapse
- [ ] Multiple assets are listed

#### 3.2 Asset Types Displayed
Verify these assets appear:
- [ ] Gates
- [ ] Street Lights
- [ ] Landscape
- [ ] Building Externals

#### 3.3 For EACH Asset - Status Buttons

##### Gates Asset:
- [ ] Three buttons visible: **Working** | **Not Working** | **N/A**
- [ ] Buttons are white by default (unselected)
- [ ] Clicking "Working" → turns **GREEN**
- [ ] Clicking "Not Working" → turns **RED**
- [ ] Clicking "N/A" → turns **GREEN**
- [ ] Only one button can be selected at a time
- [ ] Previously selected status shows correct color

##### Street Lights Asset:
- [ ] Three buttons visible: **Working** | **Not Working** | **Not checked**
- [ ] Buttons are white by default
- [ ] Clicking "Working" → turns **GREEN**
- [ ] Clicking "Not Working" → turns **RED**
- [ ] Clicking "Not checked" → turns **ORANGE**
- [ ] Only one button can be selected at a time

##### Landscape Asset:
- [ ] Three buttons visible: **Clean** | **Average** | **Poor**
- [ ] Buttons are white by default
- [ ] Clicking "Clean" → turns **GREEN**
- [ ] Clicking "Average" → turns **ORANGE**
- [ ] Clicking "Poor" → turns **RED**
- [ ] Only one button can be selected at a time

##### Building Externals Asset:
- [ ] Three buttons visible: **Good** | **Average** | **Poor**
- [ ] Buttons are white by default
- [ ] Clicking "Good" → turns **GREEN**
- [ ] Clicking "Average" → turns **ORANGE**
- [ ] Clicking "Poor" → turns **RED**
- [ ] Only one button can be selected at a time

---

### ✅ SECTION 4: DROPZONE IMAGE UPLOAD ⭐ (NEW FEATURE)

#### 4.1 Dropzone Display
For each asset, verify:
- [ ] Dropzone area is visible (dashed border, white background)
- [ ] Upload icon (cloud with arrow) is visible
- [ ] Text says: "Drag & drop images or click to browse"
- [ ] Text says: "Max 5MB per image"
- [ ] Dropzone has minimum height of 150px

#### 4.2 Drag & Drop Upload
- [ ] **Test**: Drag 1 image file onto dropzone
  - File preview appears immediately
  - Thumbnail shows image (120x120px)
  - Filename displayed below thumbnail
  - File size displayed
  - "Remove" link appears

- [ ] **Test**: Drag multiple images (2-5 images)
  - All files appear as separate previews
  - Each has its own thumbnail
  - Each has "Remove" link
  - Previews are arranged horizontally

#### 4.3 Click to Browse Upload
- [ ] Click on dropzone area
- [ ] File browser opens
- [ ] Select 1 image
- [ ] Image preview appears with thumbnail
- [ ] Select multiple images (2-5)
- [ ] All previews appear correctly

#### 4.4 Image Preview Features
- [ ] Each preview shows:
  - Thumbnail image (actual image content visible)
  - Filename (truncated if long)
  - File size (e.g., "2.5 MB")
  - "Remove" link in red text

#### 4.5 Remove Uploaded Image
- [ ] Click "Remove" link on a preview
- [ ] Image immediately disappears from list
- [ ] Can still add more images after removal
- [ ] Other images remain unaffected

---

### ✅ SECTION 5: EXISTING IMAGES ⭐ (NEW FEATURE)

#### 5.1 Existing Images Display
If inspection has existing images:
- [ ] Existing images appear below dropzone
- [ ] Thumbnails are 80x80px
- [ ] Images are arranged in a flex row with gap
- [ ] Images display correctly (not broken)

#### 5.2 Delete Existing Image
- [ ] Hover over an existing image thumbnail
- [ ] Red "X" button appears in top-right corner
- [ ] Button has opacity transition (fades in on hover)
- [ ] Click the "X" button
- [ ] Confirmation dialog appears: "Are you sure you want to delete this image?"
- [ ] Click "Cancel" → nothing happens
- [ ] Click "OK" → image disappears immediately
- [ ] Success message: "Image deleted successfully!"
- [ ] Refresh page → deleted image does NOT reappear

---

### ✅ SECTION 6: FILE VALIDATION

#### 6.1 File Size Validation
- [ ] Try uploading an image > 5MB
- [ ] Error message appears: "File is too big. Max filesize: 5MB"
- [ ] File is rejected (no preview appears)
- [ ] Dropzone remains functional

#### 6.2 File Type Validation
- [ ] Try uploading a PDF file
- [ ] Error message: "You can't upload files of this type"
- [ ] File is rejected
- [ ] Try uploading a .txt file
- [ ] Error message appears
- [ ] File is rejected

#### 6.3 File Count Validation
- [ ] Add 10 images to one dropzone (should work)
- [ ] Try to add an 11th image
- [ ] Error message: "Maximum 10 files allowed"
- [ ] 11th file is rejected
- [ ] First 10 files remain

---

### ✅ SECTION 7: NOTES FIELD

For each asset:
- [ ] Notes textarea is visible
- [ ] Label: "Note:"
- [ ] Placeholder: "Maximum allowable characters are 500."
- [ ] 3 rows height
- [ ] Can type text
- [ ] Shows existing notes if any
- [ ] Character count helper text below: "Maximum allowable characters are 500."
- [ ] Cannot type more than 500 characters

---

### ✅ SECTION 8: FORM SUBMISSION

#### 8.1 Required Fields Validation
- [ ] Leave "Block" empty → click "Update Inspection"
- [ ] Validation error appears
- [ ] General Information accordion expands automatically
- [ ] Invalid field is highlighted
- [ ] Error message shows
- [ ] Form does NOT submit

- [ ] Leave "Lead Inspector" empty → try submit
- [ ] Validation error appears

- [ ] Leave "Scheduled Date" empty → try submit
- [ ] Validation error appears

- [ ] Leave "Scheduled Time" empty → try submit
- [ ] Validation error appears

#### 8.2 Successful Submission
Complete all required fields and add images:
1. [ ] Select Block
2. [ ] Select Lead Inspector
3. [ ] Select Status
4. [ ] Enter Scheduled Date & Time
5. [ ] Expand General Assets
6. [ ] Select status for at least one asset (e.g., Gates = Working)
7. [ ] Add 2-3 images to that asset
8. [ ] Add notes (optional)
9. [ ] Click "Update Inspection"

**Expected Behavior:**
- [ ] Submit button shows loading spinner
- [ ] Button text changes to "Updating..."
- [ ] Button becomes disabled
- [ ] Progress indication appears (optional)
- [ ] Success toast notification appears (if Toastify enabled)
- [ ] Redirect to inspections list page
- [ ] Success message appears: "Inspection updated successfully"

#### 8.3 AJAX Submission Verification
Open browser Network tab (F12 → Network):
- [ ] On submit, check for POST request to `/block-inspections/{id}`
- [ ] Request payload includes:
  - Form fields
  - `photos_{asset_id}[]` (uploaded images)
  - CSRF token
- [ ] Response status: 200 OK
- [ ] Response JSON includes: `{ "success": true, "message": "..." }`

---

### ✅ SECTION 9: MULTIPLE ASSETS UPLOAD

#### 9.1 Upload to Multiple Assets
- [ ] Expand General Assets
- [ ] Add 2 images to **Gates** dropzone
- [ ] Add 3 images to **Landscape** dropzone
- [ ] Add 1 image to **Building Externals** dropzone
- [ ] Verify each dropzone shows its own images (no mixing)
- [ ] Select status for each asset with images
- [ ] Add notes to each
- [ ] Submit form
- [ ] All images for all assets are saved

#### 9.2 Verify Saved Images
After submission:
1. [ ] Go back to edit page: `/block-inspections/61/edit`
2. [ ] Expand General Assets
3. [ ] Check **Gates** asset → 2 existing images appear
4. [ ] Check **Landscape** asset → 3 existing images appear
5. [ ] Check **Building Externals** asset → 1 existing image appears
6. [ ] All images display correctly

---

### ✅ SECTION 10: ERROR HANDLING

#### 10.1 Network Error Simulation
- [ ] Open DevTools → Network tab
- [ ] Set throttling to "Offline"
- [ ] Try to submit form
- [ ] Error message appears
- [ ] Submit button re-enables
- [ ] Button text reverts to "Update Inspection"
- [ ] User can try again

#### 10.2 Invalid Data Handling
- [ ] Select a non-Property Manager user as Lead Inspector (if possible)
- [ ] Try to submit
- [ ] Error message: "Only Property Manager users can be assigned as Lead Inspector"
- [ ] Form does not submit

---

### ✅ SECTION 11: MOBILE RESPONSIVENESS

#### 11.1 Mobile View
Resize browser to mobile width (< 768px) or test on mobile device:
- [ ] Accordions work correctly
- [ ] Form fields stack vertically
- [ ] Dropzones are touch-friendly
- [ ] Can tap dropzone to open file browser
- [ ] Drag & drop works on touch devices
- [ ] Image previews display correctly
- [ ] Buttons are tap-able
- [ ] Form submits correctly

---

### ✅ SECTION 12: BROWSER COMPATIBILITY

Test on multiple browsers:
- [ ] **Chrome/Edge**: All features work
- [ ] **Firefox**: All features work
- [ ] **Safari**: All features work
- [ ] **Mobile Safari (iOS)**: All features work
- [ ] **Chrome Mobile (Android)**: All features work

---

### ✅ SECTION 13: CANCEL BUTTON

- [ ] Click "Cancel" button at bottom
- [ ] Redirects to inspection show page: `/block-inspections/61`
- [ ] No changes are saved
- [ ] No confirmation dialog (immediate redirect)

---

### ✅ SECTION 14: DATA PERSISTENCE

#### 14.1 Form Pre-population
- [ ] All existing data is pre-filled in form
- [ ] Block dropdown shows correct block
- [ ] Lead Inspector shows correct user
- [ ] Status shows correct value
- [ ] All dates and times are pre-filled
- [ ] Notes are pre-filled
- [ ] Asset statuses show correct selections (buttons colored)
- [ ] Asset notes are pre-filled
- [ ] Existing images display

#### 14.2 Partial Update
- [ ] Change ONLY the status (e.g., from Scheduled to In Progress)
- [ ] Don't change anything else
- [ ] Submit form
- [ ] Only status is updated
- [ ] All other data remains unchanged
- [ ] Existing images remain
- [ ] Asset data remains

---

## 🐛 COMMON ISSUES TO CHECK

### Issue 1: Images Not Displaying
**Check:**
- [ ] Storage link exists: `ddev exec ls -la public/storage`
- [ ] Should see symlink to `storage/app/public`
- If not, run: `ddev exec php artisan storage:link`

### Issue 2: Dropzone Not Initializing
**Check:**
- [ ] Browser console for errors
- [ ] Network tab shows `dropzone-min.js` loaded
- [ ] No JavaScript conflicts
- [ ] Clear browser cache and reload

### Issue 3: Images Not Uploading
**Check:**
- [ ] Network tab shows files in request payload
- [ ] Check Laravel logs: `storage/logs/laravel.log`
- [ ] Check file permissions: `ddev exec ls -la storage/app/public/inspection-assets`
- [ ] PHP upload limits: `upload_max_filesize` and `post_max_size`

### Issue 4: Delete Not Working
**Check:**
- [ ] Browser console for errors
- [ ] Route exists: `ddev exec php artisan route:list | grep block-inspection-images`
- [ ] CSRF token present in page source
- [ ] Server returns 200 OK response

### Issue 5: Date Picker Not Working
**Check:**
- [ ] Flatpickr library loaded in Network tab
- [ ] Calendar icon clickable
- [ ] Console for JavaScript errors
- [ ] Try different browser

---

## ✅ SUCCESS CRITERIA

All features pass when:
- [x] Page loads without errors
- [x] All form fields display correctly
- [x] All accordions work
- [x] Status buttons change colors correctly
- [x] Dropzone accepts drag & drop
- [x] Dropzone accepts click to browse
- [x] Multiple images can be uploaded
- [x] Image previews display correctly
- [x] Existing images display with delete button
- [x] Delete existing images works
- [x] File validation works (size, type, count)
- [x] Notes fields work
- [x] Form validation works
- [x] Form submits successfully with images
- [x] Success notification appears
- [x] Redirect works
- [x] Images are saved to database
- [x] Images persist after page reload
- [x] Cancel button works
- [x] Mobile responsive
- [x] No JavaScript errors in console
- [x] Works on all major browsers

---

## 📊 TEST RESULTS TEMPLATE

```
Test Date: _______________
Tester: _______________
Browser: _______________
Device: _______________

RESULTS:
- Page Load: ✅ / ❌
- General Information: ✅ / ❌
- General Assets Display: ✅ / ❌
- Status Buttons: ✅ / ❌
- Dropzone Upload: ✅ / ❌
- Drag & Drop: ✅ / ❌
- Click to Browse: ✅ / ❌
- Image Preview: ✅ / ❌
- Remove Image: ✅ / ❌
- Existing Images: ✅ / ❌
- Delete Existing: ✅ / ❌
- File Validation: ✅ / ❌
- Notes Field: ✅ / ❌
- Form Validation: ✅ / ❌
- Form Submission: ✅ / ❌
- Multiple Assets: ✅ / ❌
- Error Handling: ✅ / ❌
- Mobile View: ✅ / ❌
- Cancel Button: ✅ / ❌
- Data Persistence: ✅ / ❌

OVERALL: PASS / FAIL

NOTES:
_______________________________________________
_______________________________________________
```

---

## 🚀 NEXT STEPS AFTER VERIFICATION

If all tests pass:
1. ✅ Mark feature as ready for production
2. ✅ Update user documentation
3. ✅ Train users on new feature
4. ✅ Deploy to production environment

If tests fail:
1. ❌ Document failing tests
2. ❌ Create bug reports with details
3. ❌ Review Laravel logs for errors
4. ❌ Check browser console for errors
5. ❌ Re-test after fixes

---

## 📞 SUPPORT

For issues during testing:
- **Documentation**: See `BLOCK_INSPECTION_DROPZONE_IMPLEMENTATION.md`
- **Testing Guide**: See `BLOCK_INSPECTION_DROPZONE_TESTING_GUIDE.md`
- **Summary**: See `DROPZONE_IMPLEMENTATION_SUMMARY.md`
- **Laravel Logs**: `storage/logs/laravel.log`
- **Browser Console**: F12 → Console tab
- **Network Requests**: F12 → Network tab

---

**Good luck with your testing! 🎉**

