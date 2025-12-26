# Block Issue Photo Upload Modal - Detailed Documentation

## Overview

The Block Issue Photo Upload Modal is a comprehensive interface for uploading, managing, and viewing photos associated with block issues. It can be accessed from the Issues tab in the Block Edit page (`blocks/{id}/edit`).

## Table of Contents

1. [Modal Structure](#modal-structure)
2. [User Flow](#user-flow)
3. [Technical Implementation](#technical-implementation)
4. [Backend API Endpoints](#backend-api-endpoints)
5. [Frontend JavaScript Functions](#frontend-javascript-functions)
6. [Dropzone Configuration](#dropzone-configuration)
7. [Styling and UI Components](#styling-and-ui-components)
8. [Data Flow](#data-flow)

---

## Modal Structure

### File Location
- **View File**: `resources/views/blocks/tabs/edit/issues/modals.blade.php` (lines 267-454)
- **JavaScript**: `resources/views/blocks/tabs/edit/issues/scripts.blade.php`

### Modal HTML Structure

```html
<div class="modal fade" id="photoUploadModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header photo-upload-header">
                <!-- Camera icon, title, issue reference -->
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                <!-- Message container for alerts -->
                <!-- Issue info summary card -->
                <!-- Dropzone for image upload -->
                <!-- Upload status display -->
                <!-- Existing photos section -->
            </div>
            
            <!-- Footer -->
            <div class="modal-footer">
                <!-- Close, Clear All, Upload Photos buttons -->
            </div>
        </div>
    </div>
</div>
```

### Key Elements

1. **Modal Header** (`photo-upload-header`)
   - Gradient background (purple: `#667eea` to `#764ba2`)
   - Camera icon in styled container
   - Modal title: "Upload Photos"
   - Issue reference number display

2. **Issue Info Summary Card**
   - Displays issue details:
     - Issue title
     - Unit information
     - Issue type badge
     - Priority badge
     - Status badge
   - Styled with gradient background (`rgba(102,126,234,0.08)` to `rgba(118,75,162,0.08)`)

3. **Dropzone Container** (`#photoDropzone`)
   - Drag-and-drop area for images
   - Click-to-browse functionality
   - Visual feedback on hover/drag-over
   - Custom styling with rounded borders and purple theme

4. **Existing Photos Section** (`#existingPhotosSection`)
   - Grid layout displaying existing photos
   - Each photo shows:
     - Thumbnail (60px height)
     - Filename
     - Delete button
   - Hidden by default, shown when photos exist

5. **Modal Footer**
   - **Close**: Dismisses modal
   - **Clear All**: Clears all selected files from dropzone
   - **Upload Photos**: Initiates upload process

---

## User Flow

### Opening the Modal

1. User navigates to `blocks/{id}/edit`
2. Clicks on "Issues" tab
3. In the DataTable, finds the desired issue
4. Clicks the camera icon button (`ph-camera`) in the Actions column
5. Modal opens with issue information pre-loaded

### Uploading Photos

1. **Select Images**:
   - Option A: Drag and drop images onto the dropzone
   - Option B: Click the dropzone to open file browser
   - Multiple images can be selected at once

2. **Preview**:
   - Selected images appear as preview thumbnails in the dropzone
   - Users can remove individual images before uploading

3. **Upload**:
   - Click "Upload Photos" button
   - Progress indicators show upload status
   - Success/error messages display

4. **View Results**:
   - Success message confirms upload
   - Existing photos section updates with new images
   - Modal can be closed or more photos added

### Managing Existing Photos

1. **View Existing Photos**:
   - Photos load automatically when modal opens
   - Displayed in responsive grid (3 columns on desktop, 2 on tablet, 1 on mobile)

2. **Delete Photo**:
   - Click delete button (trash icon) on any photo
   - Confirmation modal appears
   - Confirm deletion
   - Photo is removed and section updates

---

## Technical Implementation

### Entry Point

**Function**: `window.openPhotoUploadModal(issueId)`

**Location**: `scripts.blade.php` (line 1636)

**Trigger**: Button click in DataTable:
```html
<button onclick="openPhotoUploadModal({{ $issue->id }})">
    <i class="ph-camera"></i>
</button>
```

### Initialization Process

1. **Set Current Issue ID**:
   ```javascript
   currentIssueId = issueId;
   ```

2. **Show Loading State**:
   - Upload button shows spinner
   - Button disabled during loading

3. **Load Issue Data**:
   - AJAX GET request to `/block-issues/${issueId}`
   - Populates modal with issue information

4. **Load Existing Photos**:
   - AJAX GET request to `/api/block-issues/${issueId}/photos`
   - Displays existing photos if available

5. **Initialize Dropzone**:
   - Dropzone initialized when modal is shown
   - Destroyed when modal is hidden (prevents memory leaks)

---

## Backend API Endpoints

### 1. Get Issue Details

**Route**: `GET /block-issues/{id}`

**Controller**: `BlockIssueController::show()`

**Response**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "ref_no": "ISS-2025-001",
        "issue": "Leaking pipe in kitchen",
        "issue_type": "plumbing",
        "priority_id": 3,
        "issue_status_id": 1,
        "block_unit": {
            "id": 5,
            "unit_name": "Unit 101",
            "unit_code": "101"
        }
    }
}
```

### 2. Get Existing Photos

**Route**: `GET /api/block-issues/{blockIssue}/photos`

**Controller**: `BlockIssueController::getPhotos()`

**Response**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "image_name": "image1.jpg",
            "image_path": "block-issues/images",
            "s3_status": false
        }
    ]
}
```

### 3. Upload Photos

**Route**: `POST /block-issues/{blockIssue}/photos`

**Controller**: `BlockIssueController::uploadPhotos()`

**Request**:
- Method: POST
- Content-Type: multipart/form-data
- Files: `images[]` (array of image files)

**Validation**:
- `images.*`: required, image, mimes:jpeg,png,jpg,gif, max:2048KB

**Response**:
```json
{
    "success": true,
    "message": "Photos uploaded successfully!",
    "data": [
        {
            "id": 2,
            "image_name": "1234567890_abc123.jpg",
            "image_path": "block-issues/images",
            "block_issue_id": 1
        }
    ]
}
```

**Storage**:
- Files stored in: `storage/app/public/block-issues/images/`
- File naming: `{timestamp}_{random_string}.{extension}`

### 4. Delete Photo

**Route**: `DELETE /api/block-issue-photos/{photo}`

**Controller**: `BlockIssueController::deletePhoto()`

**Response**:
```json
{
    "success": true,
    "message": "Photo deleted successfully"
}
```

**Behavior**:
- Deletes file from storage
- Removes database record
- Returns success response

---

## Frontend JavaScript Functions

### Core Functions

#### `openPhotoUploadModal(issueId)`

**Purpose**: Entry point for opening the photo upload modal

**Parameters**:
- `issueId` (number): The ID of the issue to upload photos for

**Process**:
1. Sets `currentIssueId` global variable
2. Shows loading state on trigger button
3. Calls `loadIssueForPhotoUpload(issueId)`

#### `loadIssueForPhotoUpload(issueId)`

**Purpose**: Loads issue data and existing photos

**AJAX Request**:
- URL: `/block-issues/${issueId}`
- Method: GET
- DataType: JSON

**On Success**:
1. Updates modal elements with issue data:
   - Issue title (`#photoUploadModalIssueTitle`)
   - Issue reference (`#photoUploadModalIssueRef`)
   - Issue type (`#photoUploadModalType`)
   - Unit information (`#photoUploadModalUnit`)
   - Priority badge (`#photoUploadModalPriority`)
   - Status badge (`#photoUploadModalStatus`)
2. Calls `loadExistingPhotos(issueId)`
3. Shows modal (`$('#photoUploadModal').modal('show')`)

#### `loadExistingPhotos(issueId)`

**Purpose**: Loads and displays existing photos for the issue

**AJAX Request**:
- URL: `/api/block-issues/${issueId}/photos`
- Method: GET
- DataType: JSON

**On Success**:
- Calls `displayExistingPhotos(data.data)` if photos exist
- Shows/hides `#existingPhotosSection` accordingly

#### `displayExistingPhotos(photos)`

**Purpose**: Renders existing photos in the modal

**Parameters**:
- `photos` (Array): Array of photo objects

**HTML Structure Generated**:
```html
<div class="col-md-3 col-6 mb-2">
    <div class="card">
        <img src="/storage/{image_path}/{image_name}" 
             class="card-img-top" 
             onclick="previewPhoto(...)"
             style="height: 60px; object-fit: cover; cursor: pointer;">
        <div class="card-body p-1">
            <small class="text-muted">{image_name}</small>
            <button onclick="confirmDeletePhoto({id}, '{name}')">
                <i class="ph-trash"></i>
            </button>
        </div>
    </div>
</div>
```

#### `initializePhotoDropzone()`

**Purpose**: Initializes Dropzone.js for the photo upload modal

**Configuration**:
- Element: `#photoDropzone`
- URL: `/block-issues/${currentIssueId}/photos`
- Method: POST
- Max file size: 2MB (2048KB)
- Accepted files: image/jpeg, image/png, image/jpg, image/gif
- Auto process queue: false (manual upload via button)
- Parallel uploads: 1
- Add remove links: true
- DictDefaultMessage: Custom message with icon

**Event Handlers**:
- `addedfile`: Adds preview thumbnail
- `removedfile`: Removes preview thumbnail
- `error`: Shows error message
- `success`: Shows success message and reloads existing photos
- `complete`: Handles upload completion

#### `uploadPhotos()`

**Purpose**: Manually triggers Dropzone to process queued files

**Trigger**: Click handler on `#uploadPhotosBtn` button

**Process**:
1. Checks if dropzone has files (`photoDropzone.files.length > 0`)
2. Sets the correct upload URL dynamically: `/block-issues/${currentIssueId}/photos`
3. Calls `photoDropzone.processQueue()` to start upload
4. Files are uploaded in parallel (up to 10 files at once due to `parallelUploads: 10`)
5. If no files selected, shows warning message

#### `deletePhoto(photoId)`

**Purpose**: Deletes a photo from the issue

**AJAX Request**:
- URL: `/api/block-issue-photos/${photoId}`
- Method: DELETE
- Headers: X-CSRF-TOKEN

**On Success**:
- Shows success message
- Reloads existing photos
- Updates UI

#### `confirmDeletePhoto(photoId, photoName)`

**Purpose**: Shows confirmation modal before deleting photo

**Process**:
1. Shows `#deletePhotoModal`
2. Sets up click handler on confirm button
3. Calls `deletePhoto(photoId)` on confirmation

#### `showPhotoMessage(type, message)`

**Purpose**: Displays alert messages in the modal

**Parameters**:
- `type`: success, danger, warning, info
- `message`: Message text to display

**Behavior**:
- Updates `#photoUploadMessage` element
- Auto-hides success messages after 5 seconds
- Icons change based on message type

---

## Dropzone Configuration

### Library
- **CDN**: `https://unpkg.com/dropzone@5/dist/min/dropzone.min.js`
- **CSS**: `https://unpkg.com/dropzone@5/dist/min/dropzone.min.css`

### Configuration Object

```javascript
{
    url: "#", // Disabled initially, set dynamically on upload
    method: 'POST',
    paramName: 'images',
    uploadMultiple: true, // Upload multiple files at once
    maxFiles: 10, // Maximum 10 files
    maxFilesize: 2, // 2MB per file
    acceptedFiles: "image/*", // All image types
    addRemoveLinks: true, // Show remove links on previews
    clickable: true, // Enable click to upload
    autoProcessQueue: false, // Manual upload via button
    parallelUploads: 10, // Upload up to 10 files in parallel
    dictDefaultMessage: "Drop images here or click to upload",
    dictRemoveFile: "Remove",
    dictCancelUpload: "Cancel",
    dictUploadCanceled: "Upload canceled",
    dictInvalidFileType: "You can't upload files of this type.",
    dictFileTooBig: "File is too big. Max filesize: 2MB.",
    dictMaxFilesExceeded: "You can not upload more than 10 files.",
    dictResponseError: "Server responded with an error.",
    dictCancelUploadConfirmation: "Are you sure you want to cancel this upload?",
    dictRemoveFileConfirmation: "Are you sure you want to remove this file?",
    headers: {
        'X-CSRF-TOKEN': window.csrfToken || $('meta[name="csrf-token"]').attr('content')
    }
}
```

**Note**: The `url` is initially set to `"#"` to prevent auto-upload. It's dynamically set when the "Upload Photos" button is clicked:
```javascript
photoDropzone.options.url = `/block-issues/${currentIssueId}/photos`;
photoDropzone.processQueue();
```

### Lifecycle Events

1. **addedfile**: File added to dropzone
   - Creates preview thumbnail
   - Updates upload status

2. **removedfile**: File removed from dropzone
   - Removes preview thumbnail
   - Updates upload status

3. **error**: Upload error occurred
   - Shows error message
   - Keeps file in queue for retry

4. **successmultiple**: All files uploaded successfully (when using `uploadMultiple: true`)
   - Shows success message
   - Clears dropzone files
   - Reloads existing photos
   - Auto-closes modal after 1.5 seconds

5. **errormultiple**: Upload error for multiple files
   - Shows error message with details
   - Parses validation errors if present

6. **error**: Individual file error
   - Shows error message for specific file

7. **addedfiles**: Files added to dropzone
   - Validates total file size (max 10MB total)
   - Removes files if total size exceeds limit
   - Shows warning message

---

## Styling and UI Components

### Custom CSS Classes

#### `.photo-upload-header`
- Gradient background: `linear-gradient(135deg, #667eea 0%, #764ba2 100%)`
- White text color
- No border-bottom

#### `.photo-upload-icon`
- Circular container with semi-transparent white background
- Camera icon
- Box shadow for depth

#### `.issue-upload-summary`
- Gradient background: `rgba(102,126,234,0.08)` to `rgba(118,75,162,0.08)`
- Rounded corners (14px border-radius)
- Card-style container

#### `#photoDropzone.dropzone`
- Custom border: 2px dashed `rgba(102, 126, 234, 0.45)`
- Background: `#f8f9ff`
- Border radius: 12px
- Min height: 96px
- Hover effects: darker border, lighter background, box shadow

### Responsive Design

- **Desktop (lg)**: 3 columns for photo grid
- **Tablet (md)**: 3 columns for photo grid
- **Mobile (sm)**: 2 columns for photo grid
- Modal dialog: `modal-lg` class (max-width: 900px)

### Badges

- **Priority Badge**: Color-coded based on priority level
  - Low: Success (green)
  - Normal: Info (blue)
  - High: Warning (yellow)
  - Urgent: Danger (red)
  - Critical: Dark (black)

- **Status Badge**: Color-coded based on status
  - Open: Warning (yellow)
  - In Progress: Info (blue)
  - Resolved: Success (green)
  - Closed: Secondary (gray)
  - On Hold: Danger (red)

---

## Data Flow

### Upload Flow

```
User Action
    ↓
Click "Upload Photos" Button
    ↓
photoDropzone.processQueue()
    ↓
For each file in queue:
    ↓
POST /block-issues/{id}/photos
    Content-Type: multipart/form-data
    Headers: X-CSRF-TOKEN
    Body: images[] (FormData - multiple files)
    ↓
BlockIssueController::uploadPhotos()
    ↓
Validation (image, mimes, max:2048KB)
    ↓
For each image:
    - Generate filename: {timestamp}_{random}.{ext}
    - Store file: storage/app/public/block-issues/images/
    - Create BlockIssueImage record
    ↓
Response: JSON { success: true, data: [...] }
    ↓
Dropzone success event
    ↓
Show success message
Reload existing photos
Update UI
```

### Delete Flow

```
User Action
    ↓
Click Delete Button on Photo
    ↓
confirmDeletePhoto(photoId, photoName)
    ↓
Show Confirmation Modal
    ↓
User Confirms
    ↓
DELETE /api/block-issue-photos/{photoId}
    Headers: X-CSRF-TOKEN
    ↓
BlockIssueController::deletePhoto()
    ↓
Delete file from storage
Delete BlockIssueImage record
    ↓
Response: JSON { success: true }
    ↓
Show success message
Reload existing photos
Update UI
```

---

## Error Handling

### Client-Side Validation

1. **File Type Validation**:
   - Only images allowed (`image/*` MIME type)
   - Error message: "You can't upload files of this type."
   - Handled by Dropzone's `acceptedFiles` option

2. **File Size Validation**:
   - Maximum 2MB per file
   - Error message: "File is too big. Max filesize: 2MB"
   - Handled by Dropzone's `maxFilesize` option

3. **File Count Validation**:
   - Maximum 10 files at once
   - Error message: "You can not upload more than 10 files."
   - Handled by Dropzone's `maxFiles` option

4. **Total File Size Validation**:
   - Maximum 10MB total for all files combined
   - Custom validation in `addedfiles` event handler
   - Shows warning and removes files if exceeded

5. **Network Errors**:
   - AJAX error callbacks display error messages
   - Failed uploads remain in queue for retry

### Server-Side Validation

1. **Laravel Validation Rules**:
   ```php
   'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
   ```

2. **Error Responses**:
   - 422: Validation errors (JSON response with errors)
   - 500: Server errors (error message displayed)

### User Feedback

- **Success Messages**: Green alert, auto-hide after 5 seconds
- **Error Messages**: Red alert, requires user action
- **Loading States**: Spinners, disabled buttons
- **Progress Indicators**: Dropzone built-in progress bars

---

## Integration Points

### With DataTable

- Upload button in Actions column
- After successful upload, DataTable can be refreshed to show updated photo count

### With Issue Creation/Edit Flow

- The same dropzone component is used in Step 2 of the issue creation modal
- Similar functionality but integrated into wizard flow

### With Photo Preview

- Click on photo thumbnail opens preview modal (if implemented)
- `previewPhoto()` function placeholder exists for future implementation

---

## Dependencies

### JavaScript Libraries

1. **jQuery**: Required for AJAX and DOM manipulation
2. **Dropzone.js v5**: For drag-and-drop file uploads
3. **Bootstrap 5**: For modal, buttons, badges, grid system
4. **Phosphor Icons**: For UI icons

### Backend Dependencies

1. **Laravel Framework**: Routing, validation, storage
2. **BlockIssue Model**: Eloquent model
3. **BlockIssueImage Model**: Image records
4. **Storage Facade**: File storage operations

---

## Future Enhancements

Potential improvements documented in code:

1. **Photo Preview Modal**: `previewPhoto()` function is a placeholder
2. **Image Cropping**: Could be added before upload
3. **Bulk Delete**: Select multiple photos for deletion
4. **Drag to Reorder**: Reorder photos for display
5. **Image Compression**: Client-side compression before upload
6. **Progress Per File**: Individual progress bars for each file

---

## Code References

### Key Files

1. **Modal HTML**: `resources/views/blocks/tabs/edit/issues/modals.blade.php` (267-454)
2. **JavaScript Logic**: `resources/views/blocks/tabs/edit/issues/scripts.blade.php`
   - `openPhotoUploadModal()`: 1636
   - `loadIssueForPhotoUpload()`: 1657
   - `loadExistingPhotos()`: 1712
   - `displayExistingPhotos()`: 1737
   - `initializePhotoDropzone()`: 2773
   - `deletePhoto()`: 1828
3. **Controller**: `app/Http/Controllers/BlockIssueController.php`
   - `uploadPhotos()`: 450
   - `deletePhoto()`: 520
   - `getPhotos()`: 540
4. **Routes**: `routes/web.php`
   - `POST /block-issues/{blockIssue}/photos`: 135
   - `GET /api/block-issues/{blockIssue}/photos`: 138
   - `DELETE /api/block-issue-photos/{photo}`: 139

---

## Testing Checklist

- [ ] Modal opens when clicking camera icon
- [ ] Issue information displays correctly
- [ ] Existing photos load and display
- [ ] Drag and drop works
- [ ] Click to browse works
- [ ] Multiple files can be selected
- [ ] File size validation works (>2MB rejected)
- [ ] File type validation works (non-images rejected)
- [ ] Upload button processes queue
- [ ] Success message shows after upload
- [ ] Existing photos section updates
- [ ] Delete button works
- [ ] Delete confirmation modal works
- [ ] Error handling works (network errors, server errors)
- [ ] Modal cleanup works (dropzone destroyed on close)
- [ ] CSRF token included in requests
- [ ] Responsive design works on mobile/tablet/desktop

---

*Documentation generated: January 2025*
*Last updated: Based on code analysis of current implementation*

