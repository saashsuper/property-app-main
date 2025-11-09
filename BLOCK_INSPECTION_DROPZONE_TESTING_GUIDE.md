# Block Inspection Dropzone - Testing Guide

## Quick Test Checklist

### Prerequisites
✅ DDEV environment is running
✅ Database is seeded with block inspections data
✅ Storage link is created: `ddev exec php artisan storage:link`
✅ Navigate to: https://proman.ddev.site/block-inspections/12/edit

### Test 1: Upload Multiple Images (New Feature) ✨
1. Open the Block Inspection edit page
2. Expand the "General Assets" accordion
3. For any asset (e.g., Gates, Landscape, etc.):
   - **Drag & Drop Test**:
     - Drag 2-3 image files onto the dropzone area
     - ✅ Verify: Files appear as thumbnails with preview
     - ✅ Verify: File names and sizes are displayed
   
   - **Click to Browse Test**:
     - Click on the dropzone area
     - Select 2-3 images from file browser
     - ✅ Verify: Files appear as thumbnails with preview

### Test 2: Image Preview (New Feature) ✨
1. After adding images to dropzone:
   - ✅ Verify: Each image shows a thumbnail preview (120x120px)
   - ✅ Verify: File name is displayed below thumbnail
   - ✅ Verify: File size is displayed
   - ✅ Verify: "Remove" link appears for each image

### Test 3: Remove Uploaded Images
1. Click "Remove" link on any preview image
2. ✅ Verify: Image is removed from the list
3. ✅ Verify: Can add more images after removal

### Test 4: File Validation
1. **Large File Test**:
   - Try to upload an image > 5MB
   - ✅ Verify: Error message appears: "File is too big. Max filesize: 5MB"

2. **Wrong File Type Test**:
   - Try to upload a PDF or text file
   - ✅ Verify: Error message appears: "You can't upload files of this type"

3. **Too Many Files Test**:
   - Try to add more than 10 images
   - ✅ Verify: Error message appears: "Maximum 10 files allowed"

### Test 5: Form Submission with Images
1. Fill in required fields:
   - Select Block
   - Select Lead Inspector
   - Enter Scheduled Date & Time
2. Select asset status (Working/Not Working/N/A)
3. Add images to at least one asset
4. Add notes (optional)
5. Click "Update Inspection"
6. ✅ Verify: Loading spinner appears
7. ✅ Verify: Success toast notification appears
8. ✅ Verify: Redirect to inspections list
9. ✅ Verify: Images are saved in database

### Test 6: View Existing Images (New Feature) ✨
1. Edit an inspection that already has images
2. Expand "General Assets" accordion
3. ✅ Verify: Existing images display as thumbnails (80x80px)
4. ✅ Verify: Delete button (X) appears on hover
5. ✅ Verify: Images display below the dropzone

### Test 7: Delete Existing Images (New Feature) ✨
1. Hover over an existing image thumbnail
2. Click the red "X" delete button
3. ✅ Verify: Confirmation dialog appears
4. Click "OK" to confirm
5. ✅ Verify: Image disappears from page
6. ✅ Verify: Success message: "Image deleted successfully!"
7. ✅ Verify: Image file is deleted from storage
8. Refresh the page
9. ✅ Verify: Deleted image does not reappear

### Test 8: Multiple Assets Upload
1. Add images to multiple assets (e.g., Gates AND Landscape)
2. ✅ Verify: Each dropzone maintains its own file list
3. ✅ Verify: Files don't mix between assets
4. Submit the form
5. ✅ Verify: All images for all assets are saved correctly

### Test 9: Mobile Responsiveness
1. Open page on mobile device or resize browser to mobile width
2. ✅ Verify: Dropzone is still functional
3. ✅ Verify: Touch drag & drop works
4. ✅ Verify: File browser opens on tap
5. ✅ Verify: Previews display properly

### Test 10: Form Validation
1. Leave required fields empty
2. Add images to an asset
3. Click "Update Inspection"
4. ✅ Verify: Validation errors appear
5. ✅ Verify: Form does NOT submit
6. ✅ Verify: Invalid fields are highlighted
7. ✅ Verify: Accordion with invalid fields expands automatically

### Test 11: Error Handling
1. **Network Error Simulation**:
   - Open browser DevTools > Network tab
   - Set throttling to "Offline"
   - Try to submit form
   - ✅ Verify: Error message appears
   - ✅ Verify: Submit button is re-enabled

2. **Server Error Simulation**:
   - Try to upload a corrupted image file
   - ✅ Verify: Error is handled gracefully
   - ✅ Verify: User sees meaningful error message

### Test 12: Browser Compatibility
Test on multiple browsers:
- [ ] Chrome/Edge
- [ ] Firefox
- [ ] Safari
- [ ] Mobile Safari (iOS)
- [ ] Chrome Mobile (Android)

## Common Issues & Solutions

### Issue: Images not uploading
**Check:**
- Network tab shows files in request payload
- Server logs for errors
- File permissions on storage directory
- Maximum upload size in php.ini

### Issue: Existing images not displaying
**Check:**
- Storage link exists: `ls -la public/storage`
- Image paths in database are correct
- Image files exist in storage/app/public

### Issue: Delete button not working
**Check:**
- Browser console for JavaScript errors
- Route is registered: `ddev exec php artisan route:list | grep block-inspection-images`
- CSRF token is present in page source

### Issue: Dropzone not initializing
**Check:**
- Browser console for errors
- Dropzone library is loaded: Check page source for dropzone-min.js
- No JavaScript conflicts with other libraries

## Performance Metrics

### Expected Performance:
- ✅ Dropzone initialization: < 500ms
- ✅ File preview generation: < 200ms per image
- ✅ Form submission: 2-5 seconds (depending on number of images)
- ✅ Delete image: < 1 second
- ✅ Page load with existing images: < 2 seconds

## Database Verification

After uploading images, verify in database:

```sql
-- Check inspection asset images
SELECT * FROM block_inspection_asset_images 
WHERE block_inspection_id = 12
ORDER BY created_at DESC;

-- Check image files exist
SELECT 
    id,
    CONCAT(image_path, '/', image_name) as full_path,
    created_at
FROM block_inspection_asset_images 
WHERE block_inspection_id = 12;
```

## Storage Verification

Check files exist on disk:

```bash
# List inspection images
ddev exec ls -la storage/app/public/inspection-assets/12/

# Check specific asset images
ddev exec ls -la storage/app/public/inspection-assets/12/{inspection_asset_id}/
```

## Console Log Verification

Open browser console and check for:
- ✅ "Dropzone initialized for asset X"
- ✅ "File added to dropzone_X: filename.jpg"
- ✅ "Adding N files for asset X"
- ✅ "Form is valid, processing form with Dropzone files..."
- ❌ No JavaScript errors
- ❌ No 404 errors for missing assets

## Success Criteria

All tests passed when:
- [x] Multiple images can be uploaded per asset
- [x] Drag & drop works smoothly
- [x] Click to browse works
- [x] Image previews display correctly
- [x] File validation works (size, type, count)
- [x] Form submits with files successfully
- [x] Existing images display correctly
- [x] Delete existing images works
- [x] No JavaScript errors in console
- [x] Works on multiple browsers
- [x] Mobile-friendly interface
- [x] Error handling is graceful
- [x] Performance is acceptable

## Deployment Checklist

Before deploying to production:
- [ ] Run all tests above
- [ ] Test with real production data
- [ ] Verify storage permissions
- [ ] Check disk space for images
- [ ] Test backup/restore of images
- [ ] Verify S3 integration (if enabled)
- [ ] Test rollback procedure
- [ ] Update user documentation
- [ ] Train users on new feature

## Rollback Plan

If issues occur:
1. Revert view file: `git checkout HEAD~1 resources/views/block-inspections/edit.blade.php`
2. Revert controller: `git checkout HEAD~1 app/Http/Controllers/BlockInspectionController.php`
3. Revert model: `git checkout HEAD~1 app/Models/BlockInspectionAssetImage.php`
4. Revert routes: `git checkout HEAD~1 routes/web.php`
5. Clear cache: `ddev exec php artisan cache:clear`
6. Clear views: `ddev exec php artisan view:clear`

## Support Contacts

If you encounter issues during testing:
- Check documentation: `BLOCK_INSPECTION_DROPZONE_IMPLEMENTATION.md`
- Review browser console for errors
- Check Laravel logs: `storage/logs/laravel.log`
- Review server error logs























