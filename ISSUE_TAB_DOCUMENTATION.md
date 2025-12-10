# Issue Tab - Detailed Documentation

## Table of Contents
1. [Overview](#overview)
2. [File Structure](#file-structure)
3. [Features](#features)
4. [Create Issue Flow](#create-issue-flow)
5. [Edit Issue Flow](#edit-issue-flow)
6. [Step Wizard System](#step-wizard-system)
7. [Form Fields](#form-fields)
8. [Dropzone Integration](#dropzone-integration)
9. [Unit Selection & Autocomplete](#unit-selection--autocomplete)
10. [Active Issues Table](#active-issues-table)
11. [Photo Upload Functionality](#photo-upload-functionality)
12. [DataTable Integration](#datatable-integration)
13. [Search & Filtering](#search--filtering)
14. [Export Functionality](#export-functionality)
15. [Key Functions Reference](#key-functions-reference)
16. [Modal States & Transitions](#modal-states--transitions)
17. [Known Issues & Limitations](#known-issues--limitations)
18. [Recent Updates & Changes](#recent-updates--changes)

---

## Overview

The Issue Tab is a comprehensive issue management system within the Blocks module. It allows users to:
- Create new issues with a two-step wizard (Issue Details → Image Upload)
- Edit existing issues with a two-step wizard (Issue Details → Photo Management)
- View and manage open issues for specific units
- Upload photos to issues during creation or editing
- Manage existing photos (view and remove) in Step 2
- Search and filter issues
- Export issues to PDF, Excel, or Print

The tab uses a modal-based interface with Bootstrap 5 modals (modal-lg, max-width: 900px) and integrates with DataTables for data management.

**Recent Updates (Latest)**:
- ✅ Edit mode now uses 2-step process matching create mode
- ✅ Step 2 redesigned to match photo upload modal design
- ✅ Existing photos display with remove functionality in Step 2
- ✅ Fixed dropzone initialization to prevent duplicate attachment errors
- ✅ Fixed form submission to properly handle edit mode responses
- ✅ Fixed default contact details population in edit mode
- ✅ Fixed checkbox state to auto-check when default contact details are populated
- ✅ Reduced modal width for better UX
- ✅ Step 2 shows issue summary card with badges
- ✅ "Open Issues in Same Unit" section hidden in edit mode (both steps)

---

## File Structure

### Files
```
proman/resources/views/blocks/tabs/edit/issues/
├── index.blade.php      # Main tab view with DataTable and search panel
├── modals.blade.php     # All modal definitions (Issue, Photo Upload, Delete, etc.)
└── scripts.blade.php    # JavaScript functionality (2500+ lines)
```

### Dependencies
- **Bootstrap 5**: Modal system, tabs, forms
- **DataTables**: Issue listing and management
- **Dropzone.js v5**: Image upload functionality
- **AutoComplete.js**: Unit selection autocomplete
- **jQuery**: DOM manipulation and AJAX calls
- **Font Awesome / Phosphor Icons**: UI icons

---

## Features

### Core Features
1. **Two-Step Issue Creation**
   - Step 1: Issue details form
   - Step 2: Optional image upload
   
2. **Issue Editing (2-Step Process)**
   - Edit existing issues from DataTable
   - Edit issues from "Open Issues in Same Unit" table
   - Step 1: Update issue details
   - Step 2: Manage photos (add new, view/remove existing)
   
3. **Unit Selection**
   - Autocomplete search for units
   - Auto-populates contact details from selected unit
   - Shows open issues for selected unit
   
4. **Image Management**
   - Drag & drop image upload during creation (Step 2)
   - Upload photos during edit (Step 2)
   - View existing photos with thumbnails in Step 2
   - Remove existing photos directly from Step 2
   - Photo upload modal for standalone photo management
   
5. **Search & Filter**
   - Filter by unit, status, type, priority, date range
   - Real-time DataTable filtering
   
6. **Export Options**
   - PDF export
   - Excel export
   - Print view

---

## Create Issue Flow

### Step 1: Issue Details

1. **User clicks "Report Issue" button**
   - Opens `#issueModal` in add mode
   - Modal title: "Create Issue"
   - Shows step wizard with Step 1 active

2. **Form Fields** (All required unless noted):
   - **Unit Selection**: Autocomplete search
   - **Contact Method**: Dropdown selection
   - **Assigned To**: Property manager dropdown
   - **Issue Category**: Issue type dropdown
   - **Priority**: 1-5 (Low to Critical)
   - **Problem Overview**: Text input
   - **Reported By**: Contact details text input
   - **Issue Details**: Optional textarea
   - **Default Contact Details**: Auto-populated from unit, optional

3. **Unit Selection Triggers**:
   - Auto-populates contact details
   - Loads open issues for that unit in the bottom table
   - Enables step 2 tab if unit is selected

4. **Validation**:
   - Required fields validated before proceeding
   - Visual feedback on invalid fields

5. **Button**: "Create Issue and Upload Images"
   - Creates issue via AJAX
   - Stores issue ID in hidden field
   - Enables Step 2 tab
   - Progresses to Step 2

### Step 2: Image Upload & Photo Management

1. **Tab Navigation**:
   - User clicks Step 2 tab (enabled after issue creation/update)
   - Progress bar updates to 100%
   - Footer changes to Step 2 footer (different for create vs edit mode)

2. **Issue Summary Card** (Top of Step 2):
   - Displays issue title, unit, type badge, and priority badge
   - Gradient background matching photo upload modal design
   - Responsive layout with badges on the right
   - Automatically populated from form fields

3. **Image Upload Dropzone**:
   - Styled to match photo upload modal design
   - Gradient border and background colors (#f8f9ff)
   - Hover effects with shadow
   - Rounded corners (12px border-radius)
   - Drag & drop or click to upload
   - Max 20 files, 10MB per file
   - New image previews shown in grid below dropzone

4. **Existing Photos Section**:
   - Displays existing photos in responsive grid (2 cols mobile, 3-4 cols desktop)
   - Each photo shows thumbnail with hover zoom effect
   - Remove button (red circle with X icon) on each photo
   - Click photo to open in new tab
   - Shows "No existing photos" message when empty
   - Photos loaded automatically when entering Step 2 (edit mode)

5. **Buttons** (Create Mode):
   - **Back to Issue Details**: Returns to Step 1
   - **Cancel**: Closes modal
   - **Skip & Close**: Closes modal without uploading
   - **Upload Images**: Uploads selected images

6. **Buttons** (Edit Mode):
   - **Back to Issue Details**: Returns to Step 1
   - **Cancel**: Closes modal
   - **Update Issue**: Updates issue and uploads new images (if any)

7. **Image Upload Process**:
   - Images sent via AJAX to `/block-issues/{id}/photos`
   - Success/error feedback shown
   - Modal closes after successful upload (create mode)
   - DataTable refreshes

---

## Edit Issue Flow

### Edit Mode - 2-Step Process

Edit mode now uses the same 2-step wizard as create mode for consistency.

### Edit from DataTable

1. **User clicks Edit button on DataTable row**
   - Calls `window.editIssue(issueId)`
   - Which calls `window.openIssueModal('edit', issueId)`
   - Triggers `loadIssueForEdit(issueId)`

2. **Data Loading**:
   - AJAX GET request to `/block-issues/{id}`
   - Shows loading state on edit button
   - Receives issue data in JSON format

3. **Modal Configuration**:
   - Modal title: "Edit Issue"
   - Form action: `/block-issues/{id}` with PUT method
   - **Step wizard is shown** (same as create mode)
   - Step 2 tab is enabled automatically
   - Step 1 button text: "Update Issue Details"

4. **Form Population** (Step 1):
   - Unit selection populated and autocomplete refreshed
   - All form fields populated with existing data
   - Contact details populated
   - Default contact details populated from issue data (protected from unit overwrite)
   - Default contact checkbox set if applicable
   - Uses `isInitializingEditMode` flag to prevent handlers from overwriting data

5. **Step 1 Submit**:
   - Form submits via AJAX with PUT method
   - Response: `{"success":true,"message":"Block issue updated successfully!"}`
   - On success: Proceeds to Step 2 automatically
   - Progress bar updates to 100%
   - Step 2 footer shown (edit mode version)

6. **Step 2 - Photo Management**:
   - Issue summary card displayed at top
   - Existing photos loaded and displayed
   - Dropzone initialized for new uploads
   - User can add new photos or remove existing ones
   - "Update Issue" button saves changes

### Edit from "Open Issues in Same Unit" Table

1. **User clicks Edit button in unit issues table**
   - Calls `window.editActiveIssue(issueObject)`
   - Issue object passed directly (no API call needed)

2. **Modal Configuration**:
   - Same as DataTable edit flow
   - Modal title: "Edit Issue"
   - Form configured for PUT request
   - Step wizard shown, Step 2 enabled

3. **Form Population**:
   - Direct population from issue object
   - Unit dropdown refreshed
   - All fields populated
   - Default contact details protected from overwrite

4. **Submit Flow**: Same as DataTable edit (2-step process)

---

## Step Wizard System

### Structure

```html
<!-- Progress Bar -->
<div id="custom-progress-bar" class="progress-nav">
  <div class="progress">
    <div class="progress-bar" style="width: 0%;"></div>
  </div>
  <ul class="nav nav-pills">
    <li><button id="pills-issue-details-tab">1</button></li>
    <li><button id="pills-upload-images-tab" disabled>2</button></li>
  </ul>
</div>

<!-- Tab Content -->
<div class="tab-content">
  <div id="pills-issue-details" class="tab-pane active">...</div>
  <div id="pills-upload-images" class="tab-pane">...</div>
</div>
```

### State Management

#### Step 1 State (Create Mode)
- **Active**: `pills-issue-details-tab` has `active` class
- **Progress Bar**: 0% width
- **Step 2 Tab**: Disabled
- **Footer**: `step1Footer` visible, `step2Footer` and `step2FooterEdit` hidden
- **Footer Button**: "Create Issue and Upload Images"

#### Step 1 State (Edit Mode)
- **Active**: `pills-issue-details-tab` has `active` class
- **Progress Bar**: 0% width
- **Step 2 Tab**: Enabled (automatically enabled in edit mode)
- **Footer**: `step1Footer` visible, `step2Footer` and `step2FooterEdit` hidden
- **Footer Button**: "Update Issue Details"

#### Step 2 State (After Issue Creation)
- **Active**: `pills-upload-images-tab` has `active` class
- **Progress Bar**: 100% width
- **Step 2 Tab**: Enabled
- **Footer**: `step1Footer` hidden, `step2Footer` visible, `step2FooterEdit` hidden
- **Footer Buttons**: Back, Cancel, Skip, Upload Images

#### Step 2 State (After Issue Update - Edit Mode)
- **Active**: `pills-upload-images-tab` has `active` class
- **Progress Bar**: 100% width
- **Step 2 Tab**: Enabled
- **Footer**: `step1Footer` hidden, `step2Footer` hidden, `step2FooterEdit` visible
- **Footer Buttons**: Back, Cancel, Update Issue
- **Issue Summary**: Displayed at top with issue details
- **Existing Photos**: Loaded and displayed automatically

### Navigation Functions

- `resetStepForm()`: Resets to Step 1, clears form, destroys dropzone
- `validateStep1()`: Validates required fields before proceeding
- Tab click handlers: Manage tab switching and footer visibility

---

## Form Fields

### Required Fields
1. **Unit Selection** (`issue_block_unit_id_hidden`)
   - Hidden input stores unit ID
   - Visible autocomplete shows unit code/name
   - Required for form submission

2. **Contact Method** (`contact_method_id`)
   - Dropdown from `$contactMethods` variable
   - Required

3. **Assigned To** (`assigned_to`)
   - Dropdown filtered to Property Managers
   - Required

4. **Issue Category** (`issue_type`)
   - Dropdown from `$issueTypes` variable
   - Required

5. **Priority** (`priority_id`)
   - Dropdown: 1=Low, 2=Normal, 3=High, 4=Urgent, 5=Critical
   - Default: 2 (Normal)
   - Required

6. **Problem Overview** (`issue`)
   - Text input
   - Required

7. **Reported By** (`contact_details`)
   - Text input
   - Auto-populated from unit if available
   - Required

### Optional Fields
1. **Issue Details** (`issue_details`)
   - Textarea for detailed description

2. **Default Contact Details** (`default_contact_details`)
   - Textarea (readonly)
   - Populated from unit data
   - Checkbox `use_default_contact` to toggle usage

### Hidden Fields
- `block_id`: Current block ID
- `created_issue_id`: ID of created issue (used in Step 2)
- `_method`: PUT for edit, removed for create

---

## Dropzone Integration

### Configuration

```javascript
issueDropzone = new Dropzone('#issueImageDropzone', {
  url: '#',                    // Handled by form submission
  autoProcessQueue: false,     // Manual upload trigger
  uploadMultiple: true,        // Allow multiple files
  parallelUploads: 10,         // Upload 10 files simultaneously
  maxFiles: 20,                // Maximum 20 files
  maxFilesize: 10,             // 10MB per file
  acceptedFiles: 'image/*',    // Only images
  addRemoveLinks: true,        // Show remove buttons
  clickable: true,             // Explicitly enable clicking
  previewsContainer: '#issueImagePreview'  // Preview location
});
```

### Initialization
- **Fixed**: `Dropzone.autoDiscover = false` set to prevent conflicts
- Initialized when Step 2 tab is shown (`shown.bs.tab` event)
- **Proper Cleanup**: Checks both variable and DOM element for existing instances
- Destroys existing instances before creating new one
- Destroyed when modal closes
- Error handling added for destruction process

### Styling
- Matches photo upload modal design
- Gradient border: `rgba(102, 126, 234, 0.45)`
- Background: `#f8f9ff` (light purple)
- Hover: `#eef1ff` with shadow effect
- Border radius: 12px
- Min height: 96px

### File Handling
- Files added to FormData during Step 2 submission
- Sent to `/block-issues/{id}/photos` endpoint
- Success/error feedback provided
- Works in both create and edit modes

### Fixed Issues
- ✅ **"Dropzone already attached" error**: Fixed with proper cleanup and `Dropzone.autoDiscover = false`
- ✅ **Click not triggering**: Fixed with `clickable: true` and proper element state management

---

## Unit Selection & Autocomplete

### AutoComplete.js Integration

```javascript
const autocomplete = new autoComplete({
  selector: '#issue_block_unit_id',
  data: {
    src: unitsData,  // Array of {value, label}
    key: ['label']
  },
  resultsList: { ... },
  searchEngine: 'strict',
  onSelection: function(feedback) {
    // Set hidden input with unit ID
    // Populate contact details
    // Load open issues for unit
  }
});
```

### Features
- **Search**: Type to search units by code or name
- **Keyboard Navigation**: Arrow keys to navigate, Enter to select
- **Auto-population**: Contact details and default contact populated from unit
- **Issue Loading**: Open issues for selected unit displayed in table

### Functions
- `refreshUnitsDropdown()`: Fetches units and initializes autocomplete
- `refreshUnitsDropdownForEdit(unitId)`: For edit mode, pre-selects unit
- `getUnitContactDetails(unitId)`: Fetches and populates contact info
- `fetchUnitIssues(unitId)`: Loads issues for unit
- `loadActiveIssuesForUnit(unitId)`: Updates "Open Issues in Same Unit" table

---

## Active Issues Table

### Location
Bottom section of Issue Modal (`#openIssuesInSameUnitSection`)

### Purpose
Shows open issues for the currently selected unit **only in create mode**

### Visibility Rules
- **Create Mode**: Section is visible and loads issues when a unit is selected
- **Edit Mode**: Section is **hidden** in both Step 1 and Step 2
- Section is controlled by `$('#openIssuesInSameUnitSection')` visibility

### Structure
```html
<div id="openIssuesInSameUnitSection" class="modal-body border-top">
  <div class="card border">
    <div class="card-header">
      <h6>Open Issues in Same Unit</h6>
    </div>
    <div class="card-body">
      <table id="openIssuesTable">
        <thead>
          <tr>
            <th>Ref #</th>
            <th>Issue</th>
            <th>Type</th>
            <th>Priority</th>
            <th>Reported</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="openIssuesTableBody">
          <!-- Dynamically populated -->
        </tbody>
      </table>
    </div>
  </div>
</div>
```

### Data Source
- Loaded when unit is selected **in create mode only**
- Endpoint: `/api/block-unit-active-issues?unit_id={unitId}`
- Returns JSON with `success` and `data` array of open issues
- **Note**: `loadActiveIssuesForUnit()` returns early if in edit mode

### Actions
- **Edit Button**: Calls `editActiveIssue(issueObject)` (only visible in create mode)
- Each row shows issue reference, description, type badge, priority badge, reported date

### Display Functions
- `loadActiveIssuesForUnit(unitId)`: Fetches and displays issues (skips if in edit mode)
- `displayActiveIssues(issues)`: Renders issue rows in table
- `showActiveIssuesError(message)`: Shows error message in table

### Implementation Details
- **Edit Mode Detection**: Checks for `$('#editing_issue_id').length > 0`
- **Prevention**: Function returns early if edit mode is detected
- **Unit Change Handler**: Skips loading issues if in edit mode
- **Autocomplete Selection**: Skips loading issues if in edit mode

---

## Photo Upload Functionality

### Photo Upload Modal (`#photoUploadModal`)

#### Purpose
Upload photos to an existing issue (separate from creation flow)

#### Opening
- Call `window.openPhotoUploadModal(issueId)`
- Loads issue data via AJAX
- Displays issue info in header
- Initializes dropzone for photo upload

#### Features
- **Issue Info Display**: Shows issue ref, title, unit, type, priority, status
- **Dropzone**: Separate from creation dropzone
- **Existing Photos**: Displays current photos (if any)
- **Clear All**: Clears selected files
- **Upload**: Sends to `/block-issues/{issueId}/photos`

#### Functions
- `openPhotoUploadModal(issueId)`: Opens modal and loads issue data
- `loadIssueForPhotoUpload(issueId)`: Fetches issue data
- `loadExistingPhotos(issueId)`: Loads existing photos display
- Photo preview with carousel modal
- Photo deletion with confirmation

---

## DataTable Integration

### Configuration
- **AJAX Source**: `/block-issues?block_id={blockId}&type=api`
- **Server-side Processing**: Yes
- **Columns**: Ref #, Unit, Issue, Type, Priority, Status, Reported, Assigned, Actions
- **Features**: Search, sort, pagination, export

### Functions
- `initializeDataTable()`: Sets up DataTable on page load
- `refreshBlockIssuesTable()`: Refreshes table data after create/edit/delete

### Actions Column
- **Edit**: Opens edit modal
- **Delete**: Shows confirmation, deletes issue
- **View Photos**: Opens photo upload modal (if photos exist)
- **Upload Photos**: Opens photo upload modal

### Search Integration
- Search panel filters DataTable
- Real-time filtering on change
- Filters: Unit, Status, Type, Priority, Date Range, Assigned To

---

## Search & Filtering

### Search Panel (`#searchIssuesPanel`)

#### Toggle
- Button: `#toggleSearchBtn`
- Shows/hides search panel

#### Filters
1. **Unit** (`search_unit`): Dropdown of all block units
2. **Status** (`search_state`): Open, In Progress, Resolved, Closed, On Hold
3. **Issue Type** (`search_type`): All issue types
4. **Priority** (`search_priority`): All priorities
5. **Date Range**: Start and end date pickers
6. **Assigned To** (`search_assigned`): Property managers

#### Functionality
- Form submission triggers DataTable filter
- Uses DataTables column search API
- Real-time updates on field change

---

## Export Functionality

### Export Buttons
Located in tab header, grouped in button group:

1. **PDF Export** (`#exportPdfBtn`)
   - Route: `export.pdf` with `block-issues` parameter
   - Query: `?block_id={blockId}`
   - Disabled if no issues

2. **Excel Export** (`#exportExcelBtn`)
   - Route: `export.excel` with `block-issues` parameter
   - Query: `?block_id={blockId}`
   - Disabled if no issues

3. **Print** (`#exportPrintBtn`)
   - Route: `export.print` with `block-issues` parameter
   - Query: `?block_id={blockId}`
   - Opens in new window
   - Disabled if no issues

### State Management
- Buttons enabled/disabled based on table data
- Function: `toggleExportButtons(hasData)`
- Checks if DataTable has data

---

## Key Functions Reference

### Modal Management

#### `window.openIssueModal(mode, id)`
Opens issue modal in add or edit mode.
- **Parameters**:
  - `mode`: `'add'` or `'edit'`
  - `id`: Issue ID (required for edit mode)
- **Add Mode**: Shows step wizard, resets form
- **Edit Mode**: Calls `loadIssueForEdit(id)`

#### `loadIssueForEdit(id)`
Loads issue data and populates modal for editing (2-step process).
- **AJAX**: GET `/block-issues/{id}`
- **Actions**:
  - Updates modal title to "Edit Issue"
  - Shows step wizard (same as create mode)
  - Enables Step 2 tab automatically
  - Stores `editing_issue_id` in hidden field
  - Populates all form fields
  - Protects default contact details from unit overwrite using `isInitializingEditMode` flag
  - Refreshes unit autocomplete with pre-selected unit
  - Shows modal in Step 1

#### `window.editActiveIssue(issue)`
Edits issue from "Open Issues" table (2-step process).
- **Parameters**: `issue` object with all issue data
- **Actions**:
  - Configures modal for edit
  - Shows step wizard (same as create mode)
  - Enables Step 2 tab automatically
  - Stores `editing_issue_id` in hidden field
  - Populates form directly from object
  - Protects default contact details from unit overwrite
  - No API call needed (uses provided issue object)

### Form Handling

#### `handleIssueFormSubmission(modalId, messageId, successMessage, errorMessage)`
Handles form submission for both create and edit.
- **Detection**: Checks for `_method` = PUT to determine edit mode
- **AJAX**: POST to form action URL
- **Success**: Shows message, closes modal, refreshes table
- **Error**: Shows validation errors or error message

#### `validateStep1()`
Validates Step 1 form fields.
- **Returns**: `true` if valid, `false` otherwise
- **Actions**:
  - Checks all required fields
  - Adds `is-invalid` class to invalid fields
  - Shows error message
  - Scrolls to first invalid field

#### `resetStepForm()`
Resets form to initial state (Step 1).
- **Actions**:
  - Clicks Step 1 tab
  - Disables Step 2 tab
  - Resets progress bar
  - Shows Step 1 footer, hides Step 2 footer
  - Clears created issue ID
  - Destroys dropzone

### Unit Management

#### `refreshUnitsDropdown()`
Fetches units and initializes autocomplete.
- **AJAX**: GET `/blocks/{blockId}/units`
- **Action**: Initializes AutoComplete.js with unit data

#### `refreshUnitsDropdownForEdit(unitId)`
Refreshes autocomplete and pre-selects unit (for edit mode).
- **Parameters**: `unitId` to pre-select
- **Action**: Fetches units, initializes autocomplete, selects specified unit

#### `getUnitContactDetails(unitId)`
Fetches and populates contact details for unit.
- **AJAX**: GET `/api/block-unit-contact-details?block_unit_id={unitId}`
- **Action**: 
  - Populates `contact_details` and `default_contact_details`
  - **Automatically checks** `use_default_contact` checkbox if contact details exist
  - Triggers checkbox change event to set readonly state
  - Unchecks checkbox if no contact details available

#### `loadActiveIssuesForUnit(unitId)`
Loads open issues for unit and displays in table.
- **AJAX**: GET `/api/block-unit-active-issues?unit_id={unitId}`
- **Edit Mode Check**: Returns early if `$('#editing_issue_id').length > 0`
- **Action**: Updates `#openIssuesTableBody` with issue rows (only in create mode)
- **Visibility**: Section must be visible for this to run

### Dropzone

#### `initializeIssueDropzone()`
Initializes Dropzone for Step 2 image upload (fixed implementation).
- **Element**: `#issueImageDropzone`
- **Actions**:
  - Sets `Dropzone.autoDiscover = false` to prevent conflicts
  - Checks and destroys existing instance from variable
  - Checks and destroys existing instance from DOM element
  - Creates new Dropzone instance with proper configuration
  - Sets `clickable: true` explicitly
  - Ensures pointer-events and cursor styles are set
  - Error handling for destruction process

### DataTable

#### `initializeDataTable()`
Initializes DataTable for issues listing.
- **Source**: AJAX endpoint
- **Features**: Search, sort, pagination, server-side processing

#### `window.refreshBlockIssuesTable()`
Refreshes DataTable data.
- **Action**: Reloads DataTable via AJAX

### Photo Management (Step 2)

#### `loadExistingIssuePhotos(issueId)`
Loads existing photos for an issue in Step 2.
- **AJAX**: GET `/api/block-issues/{issueId}/photos`
- **Actions**:
  - Fetches existing photos for the issue
  - Calls `displayExistingIssuePhotos()` to render
  - Shows "No existing photos" message if empty

#### `displayExistingIssuePhotos(photos)`
Displays existing photos in Step 2 with remove icons.
- **Parameters**: Array of photo objects
- **Actions**:
  - Renders photos in responsive grid (2-4 columns)
  - Each photo shows thumbnail with hover zoom effect
  - Remove button (red circle) on each photo
  - Click photo to open in new tab
  - Styled cards with shadows and rounded corners

#### `window.removeExistingIssuePhoto(photoId, photoName)`
Removes an existing photo from an issue.
- **Parameters**:
  - `photoId`: ID of photo to remove
  - `photoName`: Name for confirmation dialog
- **AJAX**: DELETE `/api/block-issue-photos/{photoId}`
- **Actions**:
  - Shows confirmation dialog
  - Deletes photo via AJAX
  - Reloads existing photos list on success

#### `populateStep2IssueSummary()`
Populates issue summary card in Step 2.
- **Actions**:
  - Extracts issue data from form fields
  - Displays issue title, unit, type, and priority
  - Formats priority badge with appropriate color
  - Called automatically when entering Step 2

### Utility

#### `showMessage(messageId, type, message)`
Shows message in specified message container.
- **Parameters**:
  - `messageId`: Container ID (e.g., 'issueMessage')
  - `type`: Alert type ('success', 'danger', 'warning', 'info')
  - `message`: Message text

#### `clearMessage(messageId)`
Clears message from container.

#### `initializeModal(modalId)`
Initializes modal to default state.
- **Actions**: Clears messages, resets step form

---

## Modal States & Transitions

### Create Mode Flow

```
1. User clicks "Report Issue"
   ↓
2. Modal opens → Step 1 active
   ↓
3. User fills form → Selects unit
   ↓
4. Unit selected → Contact details populated, issues table loaded
   ↓
5. User clicks "Create Issue and Upload Images"
   ↓
6. Issue created → Issue ID stored → Step 2 enabled
   ↓
7. User navigates to Step 2 → Dropzone initialized
   ↓
8. User uploads images (optional) → Submits
   ↓
9. Images uploaded → Modal closes → Table refreshes
```

### Edit Mode Flow (2-Step Process)

```
1. User clicks Edit button (DataTable or Unit Issues table)
   ↓
2. Modal opens → Loading state shown
   ↓
3. Issue data loaded (if from DataTable)
   ↓
4. Modal configured for edit:
   - Title: "Edit Issue"
   - Step wizard shown (same as create)
   - Step 2 tab enabled
   - Form action: PUT /block-issues/{id}
   - editing_issue_id stored
   ↓
5. Step 1: Form populated with issue data
   - Default contact details protected from overwrite
   - isInitializingEditMode flag prevents handlers from overwriting
   ↓
6. User clicks "Update Issue Details"
   ↓
7. Issue updated → Response: {"success":true,"message":"..."}
   ↓
8. Automatically proceeds to Step 2
   - Progress bar: 100%
   - Step 2 footer shown (edit mode)
   - Issue summary card displayed
   - Existing photos loaded
   ↓
9. User manages photos (add/remove)
   ↓
10. User clicks "Update Issue"
    ↓
11. New images uploaded (if any) → Modal closes → Table refreshes
```

### State Variables

- **Mode**: `'add'` or `'edit'` (determined by form `_method` input)
- **Current Step**: 1 or 2 (both create and edit modes)
- **Issue ID**: 
  - `#created_issue_id` (create mode, Step 2)
  - `#editing_issue_id` (edit mode, both steps)
- **Dropzone**: `issueDropzone` variable (null when not initialized)
- **Initialization Flag**: `isInitializingEditMode` (prevents handlers from overwriting during edit setup)

---

## Known Issues & Limitations

### ✅ Fixed Issues

1. **✅ Edit Mode Footer** - FIXED
   - **Solution**: Added `step2FooterEdit` with "Update Issue" button
   - **Location**: `modals.blade.php` and footer switching logic
   - **Status**: Working correctly

2. **✅ Dropzone Initialization** - FIXED
   - **Solution**: Added `Dropzone.autoDiscover = false` and proper cleanup
   - **Location**: `initializeIssueDropzone()` function
   - **Status**: No more duplicate attachment errors

3. **✅ Edit Mode Step Wizard** - FIXED
   - **Solution**: Edit mode now uses 2-step process like create mode
   - **Location**: `loadIssueForEdit()` and `editActiveIssue()` functions
   - **Status**: Consistent UI across create and edit

4. **✅ Form Submission in Edit Mode** - FIXED
   - **Solution**: Updated button handler to detect edit mode and handle response correctly
   - **Location**: `#createIssueAndUploadBtn` click handler
   - **Status**: Properly handles `{"success":true,"message":"..."}` response

5. **✅ Default Contact Details in Edit Mode** - FIXED
   - **Solution**: Added `isInitializingEditMode` flag to prevent overwrite
   - **Location**: `loadIssueForEdit()`, `editActiveIssue()`, and related handlers
   - **Status**: Default contact details properly preserved

6. **✅ Step 2 Navigation in Edit Mode** - FIXED
   - **Solution**: Updated tab handlers to check both `created_issue_id` and `editing_issue_id`
   - **Location**: `show.bs.tab` and `shown.bs.tab` handlers
   - **Status**: Navigation works correctly in edit mode

7. **✅ Image Upload in Edit Mode** - FIXED
   - **Solution**: Step 2 enabled in edit mode with photo management
   - **Location**: Edit mode initialization
   - **Status**: Can upload and manage photos during edit

8. **✅ Default Contact Checkbox State** - FIXED
   - **Solution**: `getUnitContactDetails()` now automatically checks checkbox when details are populated
   - **Location**: `getUnitContactDetails()` function
   - **Status**: Checkbox state synchronized with contact details population

9. **✅ "Open Issues in Same Unit" Section Visibility** - FIXED
   - **Solution**: Section hidden in edit mode with multiple safeguards
   - **Location**: `loadIssueForEdit()`, `editActiveIssue()`, `loadActiveIssuesForUnit()`, unit change handlers
   - **Status**: Section properly hidden in edit mode, visible in create mode
   - **Implementation**:
     - Section explicitly hidden when entering edit mode
     - `loadActiveIssuesForUnit()` returns early if in edit mode
     - Unit change handler skips loading issues in edit mode
     - Autocomplete selection handler skips loading issues in edit mode
     - Section shown again in create mode and on form reset

### Current Limitations

1. **Form Validation**
   - Validation only for Step 1 in create mode
   - No validation feedback in edit mode Step 1
   - Could add validation for edit mode

2. **Unit Issues Table**
   - Only shows open issues
   - No pagination for large lists
   - Max height restricts visibility

3. **Modal Width**
   - Currently set to `modal-lg` (900px max-width)
   - May be narrow for some content on smaller screens
   - Consider responsive adjustments

### Recent Improvements

1. **✅ Step 2 Redesign**
   - Matches photo upload modal design
   - Issue summary card with badges
   - Improved dropzone styling
   - Better existing photos display

2. **✅ Modal Width Reduction**
   - Changed from `modal-xl` (95vw) to `modal-lg` (900px)
   - More focused and compact interface

3. **✅ Photo Management in Step 2**
   - View existing photos with thumbnails
   - Remove photos directly from Step 2
   - Add new photos via dropzone
   - Consistent experience in create and edit modes

---

## API Endpoints Used

### Issue Management
- `GET /block-issues/{id}` - Get issue details
- `POST /block-issues` - Create issue
- `PUT /block-issues/{id}` - Update issue
- `DELETE /block-issues/{id}` - Delete issue
- `GET /block-issues?block_id={id}&type=api` - List issues (DataTable)

### Unit Management
- `GET /blocks/{blockId}/units` - Get units for block
- `GET /blocks/units/{unitId}/contact-details` - Get unit contact details
- `GET /block-issues/unit/{unitId}` - Get open issues for unit

### Photo Management
- `POST /block-issues/{id}/photos` - Upload photos
- `GET /block-issues/{id}/photos` - Get issue photos
- `DELETE /block-issues/photos/{photoId}` - Delete photo

### Export
- `GET /export/pdf/block-issues?block_id={id}` - PDF export
- `GET /export/excel/block-issues?block_id={id}` - Excel export
- `GET /export/print/block-issues?block_id={id}` - Print view

---

## CSS Classes & IDs Reference

### Modal Elements
- `#issueModal` - Main issue modal (modal-lg, max-width: 900px)
- `#issueModalLabel` - Modal title
- `#issueForm` - Issue form
- `#issueMessage` - Message container
- `#custom-progress-bar` - Step wizard progress bar
- `#pills-issue-details-tab` - Step 1 tab button
- `#pills-upload-images-tab` - Step 2 tab button
- `#pills-issue-details` - Step 1 content
- `#pills-upload-images` - Step 2 content
- `#step1Footer` - Step 1 footer
- `#step2Footer` - Step 2 footer (create mode)
- `#step2FooterEdit` - Step 2 footer (edit mode)
- `#step2IssueTitle` - Issue title in Step 2 summary
- `#step2IssueUnit` - Unit name in Step 2 summary
- `#step2IssueType` - Issue type badge in Step 2 summary
- `#step2IssuePriority` - Priority badge in Step 2 summary
- `#existingIssuePhotos` - Container for existing photos in Step 2
- `#noExistingPhotos` - Message when no existing photos

### Form Fields
- `#issue_block_unit_id` - Unit autocomplete input
- `#issue_block_unit_id_hidden` - Unit ID hidden input
- `#contact_method_id` - Contact method dropdown
- `#assigned_to` - Assigned to dropdown
- `#issue_type` - Issue type dropdown
- `#priority_id` - Priority dropdown
- `#issue` - Problem overview input
- `#contact_details` - Reported by input
- `#issue_details` - Issue details textarea
- `#default_contact_details` - Default contact textarea
- `#use_default_contact` - Default contact checkbox
- `#created_issue_id` - Created issue ID (hidden)

### Dropzone
- `#issueImageDropzone` - Step 2 dropzone
- `#issueImagePreview` - Image preview container
- `#photoDropzone` - Photo upload modal dropzone

### Tables
- `#blockIssuesTable` - Main issues DataTable
- `#openIssuesInSameUnitSection` - Container for "Open Issues in Same Unit" section
- `#openIssuesTable` - Unit open issues table
- `#openIssuesTableBody` - Unit issues table body

### Buttons
- `#createIssueAndUploadBtn` - Create and proceed to Step 2
- `#uploadImagesBtn` - Upload images in Step 2
- `#skipUploadBtn` - Skip image upload
- `#issueSubmitBtn` - Submit button (edit mode)

---

## Event Handlers

### Modal Events
- `shown.bs.modal` - Modal fully shown
- `hidden.bs.modal` - Modal fully hidden
- `shown.bs.tab` - Tab shown (for Step 2 dropzone init)

### Form Events
- `submit` - Form submission
- `change` - Field changes (unit selection, contact method, etc.)

### Button Clicks
- `#createIssueAndUploadBtn` - Create issue and go to Step 2
- `#uploadImagesBtn` - Upload images
- `#skipUploadBtn` - Skip upload
- `.previestab` - Go to previous tab

---

## Testing Checklist

### Create Flow
- [ ] Modal opens in add mode
- [ ] Step 1 form displays correctly
- [ ] Unit selection works
- [ ] Contact details auto-populate
- [ ] Open issues table loads
- [ ] Validation works for required fields
- [ ] Issue creates successfully
- [ ] Step 2 enables after creation
- [ ] Dropzone initializes in Step 2
- [ ] Images upload successfully
- [ ] Modal closes and table refreshes

### Edit Flow
- [ ] Edit from DataTable works
- [ ] Edit from unit issues table works
- [ ] Modal opens in edit mode
- [ ] Step wizard is shown (2-step process)
- [ ] "Open Issues in Same Unit" section is hidden
- [ ] All fields populate correctly
- [ ] Default contact details checkbox is checked if details exist
- [ ] Form submits with PUT method
- [ ] Issue updates successfully
- [ ] Automatically navigates to Step 2 after update
- [ ] Step 2 shows existing photos
- [ ] Can add new photos or remove existing ones
- [ ] Modal closes and table refreshes

### Edge Cases
- [ ] Cancel during Step 1
- [ ] Cancel during Step 2
- [ ] Skip image upload
- [ ] Edit without changing fields
- [ ] Unit selection with no contact details
- [ ] Unit with no open issues
- [ ] Form validation errors
- [ ] Network errors during submission

---

## Recent Updates & Changes

### Version Updates (Latest)

#### Edit Mode 2-Step Process
- **Changed**: Edit mode now uses the same 2-step wizard as create mode
- **Impact**: Consistent user experience across create and edit flows
- **Files Modified**: `scripts.blade.php`, `modals.blade.php`

#### Step 2 Redesign
- **Changed**: Step 2 redesigned to match photo upload modal
- **Features Added**:
  - Issue summary card with gradient background
  - Styled dropzone matching photo upload modal
  - Existing photos display with thumbnails
  - Remove photo functionality
- **Files Modified**: `modals.blade.php`, `index.blade.php`, `scripts.blade.php`

#### Dropzone Fixes
- **Fixed**: "Dropzone already attached" error
- **Solution**: 
  - Added `Dropzone.autoDiscover = false`
  - Proper cleanup of existing instances
  - Check both variable and DOM element
- **Files Modified**: `scripts.blade.php`

#### Form Submission Fixes
- **Fixed**: Edit mode form submission handling
- **Solution**: 
  - Detects edit mode correctly
  - Handles `{"success":true,"message":"..."}` response format
  - Properly navigates to Step 2 after update
- **Files Modified**: `scripts.blade.php`

#### Default Contact Details Fix
- **Fixed**: Default contact details not populating in edit mode
- **Solution**: 
  - Added `isInitializingEditMode` flag
  - Prevents unit contact details from overwriting saved data
  - Proper initialization order
  - **Updated**: `getUnitContactDetails()` now automatically checks checkbox when details are populated
- **Files Modified**: `scripts.blade.php`

#### Default Contact Checkbox Auto-Check
- **Fixed**: Checkbox not checked when default contact details are populated
- **Solution**: 
  - Modified `getUnitContactDetails()` to set checkbox state based on contact details availability
  - Triggers change event to update readonly state
  - Checkbox state synchronized with contact details population
- **Files Modified**: `scripts.blade.php`

#### "Open Issues in Same Unit" Section Visibility
- **Fixed**: Section showing in edit mode
- **Solution**: 
  - Section explicitly hidden when entering edit mode
  - `loadActiveIssuesForUnit()` returns early if in edit mode
  - Unit change handler skips loading issues in edit mode
  - Autocomplete selection handler skips loading issues in edit mode
  - Multiple safeguards ensure section stays hidden throughout edit process
  - Section shown in create mode and on form reset
- **Files Modified**: `scripts.blade.php`, `modals.blade.php`

#### Modal Width Reduction
- **Changed**: Modal width from `modal-xl` (95vw) to `modal-lg` (900px)
- **Impact**: More focused and compact interface
- **Files Modified**: `modals.blade.php`

#### Step 2 Navigation Fix
- **Fixed**: Step 2 tab blocked in edit mode
- **Solution**: 
  - Updated tab handlers to check both `created_issue_id` and `editing_issue_id`
  - Proper mode detection
- **Files Modified**: `scripts.blade.php`

### New Functions Added

1. **`loadExistingIssuePhotos(issueId)`**
   - Loads existing photos for display in Step 2
   - Endpoint: `GET /api/block-issues/{issueId}/photos`

2. **`displayExistingIssuePhotos(photos)`**
   - Renders existing photos with thumbnails and remove buttons
   - Responsive grid layout

3. **`window.removeExistingIssuePhoto(photoId, photoName)`**
   - Removes existing photo from issue
   - Endpoint: `DELETE /api/block-issue-photos/{photoId}`

4. **`populateStep2IssueSummary()`**
   - Populates issue summary card in Step 2
   - Extracts data from form fields

### New UI Elements

1. **Issue Summary Card** (`#step2IssueTitle`, `#step2IssueUnit`, `#step2IssueType`, `#step2IssuePriority`)
   - Displays at top of Step 2
   - Gradient background matching photo upload modal

2. **Existing Photos Container** (`#existingIssuePhotos`)
   - Responsive grid for photo thumbnails
   - Remove buttons on each photo

3. **Edit Mode Footer** (`#step2FooterEdit`)
   - Separate footer for edit mode Step 2
   - "Update Issue" button

### CSS Updates

1. **Dropzone Styling**
   - Gradient borders and backgrounds
   - Hover effects with shadows
   - Rounded corners (12px)

2. **Issue Summary Card**
   - Gradient background
   - Badge styling
   - Responsive layout

3. **Existing Photos Cards**
   - Shadow effects
   - Hover zoom
   - Rounded corners

---

## Testing Insights & Verification

### Key Functionality Tests

#### 1. Edit Mode - "Open Issues in Same Unit" Section Visibility
**Test Scenario**: Open issue in edit mode and verify section is hidden

**Steps**:
1. Click "Edit" button on any issue in DataTable
2. Modal opens in edit mode
3. Verify "Open Issues in Same Unit" section is **NOT visible** at bottom
4. Select a unit (if not already selected)
5. Verify section remains hidden
6. Click "Update Issue Details" to proceed to Step 2
7. Verify section remains hidden in Step 2

**Expected Result**: Section should be hidden in both Step 1 and Step 2 of edit mode

**Code Verification**:
- ✅ `loadIssueForEdit()`: Sets `$('#openIssuesInSameUnitSection').hide()` at line 610
- ✅ `editActiveIssue()`: Sets `$('#openIssuesInSameUnitSection').hide()` at line 2958
- ✅ `loadActiveIssuesForUnit()`: Returns early if edit mode detected (checks `$('#editing_issue_id').length > 0`)
- ✅ Unit change handler: Skips `loadActiveIssuesForUnit()` if in edit mode
- ✅ Autocomplete handler: Skips `loadActiveIssuesForUnit()` if in edit mode

#### 2. Create Mode - "Open Issues in Same Unit" Section Visibility
**Test Scenario**: Open issue creation modal and verify section is visible

**Steps**:
1. Click "Report Issue" button
2. Modal opens in create mode
3. Verify "Open Issues in Same Unit" section **IS visible** at bottom (shows "Select a unit to view open issues")
4. Select a unit from autocomplete
5. Verify section loads and displays open issues for that unit

**Expected Result**: Section should be visible and functional in create mode

**Code Verification**:
- ✅ `openIssueModal('add')`: Sets `$('#openIssuesInSameUnitSection').show()` at line 556
- ✅ `resetStepForm()`: Sets `$('#openIssuesInSameUnitSection').show()` at line 774
- ✅ `loadActiveIssuesForUnit()`: Executes normally in create mode

#### 3. Default Contact Details Checkbox State
**Test Scenario**: Verify checkbox is automatically checked when contact details are populated

**Steps**:
1. Open issue creation modal
2. Select a unit that has contact details (mobile, email, etc.)
3. Verify `default_contact_details` textarea is populated
4. Verify `use_default_contact` checkbox is **automatically checked**
5. Verify textarea is readonly (due to checkbox being checked)
6. Uncheck the checkbox
7. Verify textarea becomes editable
8. Check the checkbox again
9. Verify textarea becomes readonly again

**Expected Result**: Checkbox should be checked automatically when contact details exist

**Code Verification**:
- ✅ `getUnitContactDetails()`: Sets `$('#use_default_contact').prop('checked', true)` if contact details exist (around line 1550+)
- ✅ Triggers change event: `$('#use_default_contact').trigger('change')` to update readonly state

#### 4. Edit Mode - Default Contact Details Population
**Test Scenario**: Verify default contact details are populated from unit (not saved issue data) in edit mode

**Steps**:
1. Open an issue in edit mode that has a unit assigned
2. Verify default contact details are populated from the **unit** (not from saved issue data)
3. Verify checkbox is checked if contact details exist
4. Change the unit to a different one
5. Verify default contact details update to the new unit's contact details
6. Verify checkbox state updates accordingly

**Expected Result**: Unit contact details should always take precedence in all scenarios

**Code Verification**:
- ✅ `loadIssueForEdit()`: Calls `getUnitContactDetails()` after unit is set (line 674)
- ✅ `editActiveIssue()`: Calls `getUnitContactDetails()` after unit is set
- ✅ `getUnitContactDetails()`: Always populates from unit data, never from saved issue data

#### 5. Step 2 Navigation in Edit Mode
**Test Scenario**: Verify Step 2 is accessible and functional in edit mode

**Steps**:
1. Open issue in edit mode
2. Verify Step 2 tab is enabled (not disabled)
3. Click "Update Issue Details" to update Step 1
4. Verify automatically navigates to Step 2 after successful update
5. Verify Step 2 shows:
   - Issue summary card at top
   - Dropzone for new uploads (50% width)
   - Existing photos section (50% width) with thumbnails
   - Remove buttons on existing photos
6. Verify "Update Issue" button is present (not "Upload Images")

**Expected Result**: Step 2 should be fully functional in edit mode with proper UI

**Code Verification**:
- ✅ Step 2 tab enabled: `step2Tab.disabled = false` in edit mode initialization
- ✅ Tab handlers check `editing_issue_id` to allow navigation
- ✅ Step 2 footer: `step2FooterEdit` shown (not `step2Footer`)

### Edge Cases

1. **Unit without contact details**: Checkbox should be unchecked, textarea empty
2. **Switching units in edit mode**: Contact details should update, section stays hidden
3. **Edit mode → Cancel → Create mode**: Section should show in create mode
4. **Multiple rapid unit changes**: Should not cause race conditions or multiple API calls

### Performance Considerations

- `loadActiveIssuesForUnit()` returns early in edit mode (no unnecessary API calls)
- Multiple safeguards prevent section from showing accidentally
- Checkbox state managed efficiently with single source of truth (`getUnitContactDetails()`)

---

## Conclusion

This documentation provides a comprehensive overview of the Issue Tab functionality. For specific implementation details, refer to the source files:

- **UI Structure**: `modals.blade.php`
- **Main View**: `index.blade.php`
- **Functionality**: `scripts.blade.php`

For questions or issues, refer to the "Known Issues & Limitations" section. Most previously known issues have been fixed in recent updates.

### Quick Reference

- **Modal Size**: `modal-lg` (900px max-width)
- **Steps**: 2-step process for both create and edit
- **Step 2 Features**: Issue summary, dropzone, existing photos management
- **Edit Mode**: Full 2-step process with photo management
- **Dropzone**: Fixed initialization, styled to match photo upload modal

