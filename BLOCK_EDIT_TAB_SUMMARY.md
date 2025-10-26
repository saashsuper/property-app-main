# Block Edit - Quick Reference Guide

## 🗂️ Tab Overview

```
┌─────────────────────────────────────────────────────────────────────┐
│                      BLOCK EDIT PAGE                                 │
│                    /blocks/{block}/edit                              │
└─────────────────────────────────────────────────────────────────────┘
                                  │
     ┌────────────────────────────┼────────────────────────────┐
     │                            │                            │
     ▼                            ▼                            ▼
┌─────────┐              ┌─────────────┐              ┌──────────┐
│  Tab 1  │              │   Tab 2-9   │              │   DB     │
│ Basic   │──────────────│   Data      │──────────────│  Models  │
│ Details │   Updates    │   Tabs      │  CRUD Ops    │          │
└─────────┘              └─────────────┘              └──────────┘
     │                            │                            │
     │                            │                            │
     ▼                            ▼                            ▼
 PUT /blocks/{id}         AJAX API Calls            Relationships
```

---

## 📊 Tab Breakdown

| # | Tab Name | Controller | Model | Key Features |
|---|----------|------------|-------|-------------|
| 1 | **Basic Details** | BlockController | Block | Form submission, Country-State dropdown |
| 2 | **Block Information** | BlockInformationController | BlockInformation | DataTables, Export, CRUD modal |
| 3 | **Building/Core** | BlockBuildingController | BlockBuilding | DataTables, Export, CRUD modal |
| 4 | **Units** | BlockUnitController | BlockUnit | DataTables, Export, Excel import, CRUD |
| 5 | **Contractors** | BlockContractorController | BlockContractor | DataTables, Export, CRUD modal |
| 6 | **Site Visit** | BlockVisitController | BlockVisit | DataTables, Export, Status tracking |
| 7 | **Inspections** | BlockInspectionController | BlockInspection | DataTables, Export, Status workflow |
| 8 | **Issues** | BlockIssueController | BlockIssue | DataTables, Search panel, Photo upload |
| 9 | **Work Orders** | BlockWorkOrderController | BlockWorkOrder | DataTables, Export, CRUD modal |

---

## 🎯 Feature Matrix

| Feature | Tab 1 | Tab 2 | Tab 3 | Tab 4 | Tab 5 | Tab 6 | Tab 7 | Tab 8 | Tab 9 |
|---------|-------|-------|-------|-------|-------|-------|-------|-------|-------|
| **Form Edit** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **DataTables** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Add New** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Edit Records** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Delete Records** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Export PDF** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Export Excel** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Print** | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Search/Filter** | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **File Upload** | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ |
| **Status Badges** | ❌ | ❌ | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ | ✅ |
| **View Details** | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ | ✅ |

---

## 🔄 Common Patterns

### Pattern 1: Data Tab Structure
```
┌─────────────────────────────────────────────┐
│  Header (Gradient with title)              │
│  [Export Buttons] [Add Button]             │
└─────────────────────────────────────────────┘
│                                             │
│  DataTable                                  │
│  ┌───────┬───────┬───────┬─────────┐      │
│  │ Col 1 │ Col 2 │ Col 3 │ Actions │      │
│  ├───────┼───────┼───────┼─────────┤      │
│  │ Data  │ Data  │ Data  │ 🔧 🗑️   │      │
│  └───────┴───────┴───────┴─────────┘      │
│                                             │
└─────────────────────────────────────────────┘
```

### Pattern 2: Modal CRUD Flow
```
[Add Button] ──► Open Modal ──► Fill Form ──► Submit AJAX
                                                    │
                                                    ▼
                                           Validate & Save
                                                    │
                                                    ▼
                                        Update DataTable ──► Show Success
```

### Pattern 3: Export Flow
```
[Export Button] ──► Check Data Availability
                           │
                           ├─► Has Data ──► Generate File ──► Download
                           │
                           └─► No Data ──► Disabled State
```

---

## 📝 Tab Details

### Tab 1: Basic Details
```yaml
Type: Form Submission
Method: PUT
Endpoint: /blocks/{block}
Features:
  - Text inputs
  - Dropdowns (Block Type, Country, State, Manager)
  - Number inputs
  - Textareas
  - Dynamic country-state dependency
  - Inline validation
```

### Tab 2: Block Information
```yaml
Type: DataTable CRUD
Controller: BlockInformationController
Columns: [Type, Description, Date, Added By, Actions]
Actions: [Add, Edit, View, Delete]
Export: [PDF, Excel, Print]
```

### Tab 3: Building/Core
```yaml
Type: DataTable CRUD
Controller: BlockBuildingController
Columns: [Name, Type, Floor, Roof Type, Lifts, Status, Actions]
Actions: [Add, Edit, Delete]
Export: [PDF, Excel, Print]
```

### Tab 4: Units
```yaml
Type: DataTable CRUD + Bulk Upload
Controller: BlockUnitController
Columns: [Code, Name, Type, Owner, Email, Resident, Mobile, Agent, Actions]
Actions: [Add, Upload Excel, Edit, Delete]
Export: [PDF, Excel, Print]
Special: Excel template download
```

### Tab 5: Contractors
```yaml
Type: DataTable CRUD
Controller: BlockContractorController
Columns: [Name, Email, Contract Type, Status, Actions]
Actions: [Add, Edit, Delete]
Export: [PDF, Excel, Print]
Status: [Default, Active]
```

### Tab 6: Site Visit
```yaml
Type: DataTable CRUD
Controller: BlockVisitController
Columns: [Reference, Date, User, Reason, Status, Notes, Actions]
Actions: [Add, Edit, View Details, Delete]
Export: [PDF, Excel, Print]
Status: [Completed, In Progress, Scheduled]
```

### Tab 7: Inspections
```yaml
Type: DataTable CRUD
Controller: BlockInspectionController
Columns: [Reference, Date, Inspector, Status, Notes, Actions]
Actions: [Add, Edit, Delete, Start, Complete]
Export: [PDF, Excel, Print]
Status: [Created, In Progress, Work Order, Completed, Invoiced]
```

### Tab 8: Issues
```yaml
Type: DataTable CRUD + Advanced Search
Controller: BlockIssueController
Columns: [ID, Title, Unit, Type, Priority, Status, Date, Actions]
Actions: [Add, Edit, Upload Photos, Delete, View]
Export: [PDF, Excel, Print]
Search: [Unit, Status, Type, Priority, Keyword]
Priority: [Low, Normal, High, Urgent, Critical]
Status: [Open, In Progress, Resolved, Closed, On Hold]
Special: Dropzone photo upload, Contact details
```

### Tab 9: Work Orders
```yaml
Type: DataTable CRUD
Controller: BlockWorkOrderController
Columns: [Work Order #, Title, Priority, Status, Date, Actions]
Actions: [Add, View, Edit, Delete]
Export: [PDF, Excel, Print]
Priority: [Low, Normal, High, Urgent, Critical]
Status: [Open, In Progress, Completed]
```

---

## 🎨 UI Components

### Bootstrap Components Used
- **Nav Pills:** Tab navigation
- **Modals:** CRUD forms
- **Badges:** Status indicators
- **Buttons:** Actions and exports
- **Forms:** Input controls
- **Tables:** Data display
- **Alerts:** Error/success messages

### Custom Styling
```css
/* Gradient Headers */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);

/* Tab Arrow Indicator */
.nav-link.active::after { 
    border-top: 8px solid #495057; 
}

/* Export Button Disabled */
.btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
```

---

## 🔌 API Endpoints Summary

### Basic Pattern
```
GET    /resource              - List
GET    /resource/{id}         - Show
POST   /resource              - Create
PUT    /resource/{id}         - Update
DELETE /resource/{id}         - Destroy

GET    /resource/block/{id}   - Filter by block
```

### Export Pattern
```
GET /export/pdf/{resource}?block_id={id}
GET /export/excel/{resource}?block_id={id}
GET /export/print/{resource}?block_id={id}
```

---

## 🗄️ Database Structure

### Core Tables
```
blocks
├── block_information
├── block_buildings
├── block_units
├── block_contractors
├── block_visits
│   └── block_visit_teams
├── block_inspections
│   ├── block_inspection_teams
│   └── block_inspection_values
├── block_issues
│   ├── block_issue_actions
│   └── block_issue_images
└── block_work_orders
    └── block_work_order_images
```

### Lookup Tables
```
- block_types
- building_types
- block_unit_types
- block_contractor_types
- block_information_types
- job_reasons
- job_statuses
- issue_statuses
- issue_types
- priorities
- contact_methods
```

---

## 🚀 Performance Tips

1. **Eager Loading:** Used in controller edit method
2. **DataTables:** Client-side pagination by default
3. **AJAX:** Reduces full page reloads
4. **Caching:** Cache dropdown data
5. **Indexes:** Ensure foreign keys are indexed
6. **Lazy Loading:** Load tab data on demand

---

## 🔒 Security Checklist

- ✅ CSRF protection on all forms
- ✅ Role-based access control
- ✅ Input validation (server-side)
- ✅ SQL injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade templating)
- ✅ File upload validation
- ✅ Soft deletes with tracking

---

## 📱 Responsive Behavior

| Screen Size | Behavior |
|-------------|----------|
| **Desktop (>1200px)** | Full tab display, all columns visible |
| **Tablet (768-1199px)** | Scrollable tabs, some columns hidden |
| **Mobile (<768px)** | Compact tabs, minimal columns, touch-optimized |

---

## 🎯 Quick Access

### File Locations
```
Controller:  app/Http/Controllers/BlockController.php (edit method)
Main View:   resources/views/blocks/edit.blade.php
Tab Views:   resources/views/blocks/tabs/edit/{tab}/index.blade.php
Routes:      routes/web.php (line 54, 88-173)
Models:      app/Models/Block*.php
```

### Key Routes
```php
GET  /blocks/{block}/edit        - Edit page
PUT  /blocks/{block}              - Update basic details
GET  /api/states/{countryId}      - Get states by country
```

---

## 📞 Support & Documentation

**Main Documentation:**
- [Complete Analysis](BLOCK_EDIT_FEATURE_ANALYSIS.md)
- [Controllers Summary](BLOCKS_CONTROLLERS_SUMMARY.md)
- [Issue Tab Details](ISSUE_TAB_ANALYSIS.md)

**Related Features:**
- Block Index/List
- Block Create
- Block Show/Details
- Dashboard Integration

---

**Last Updated:** October 12, 2025

