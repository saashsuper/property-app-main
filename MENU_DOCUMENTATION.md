# Menu Section Documentation

## Overview

The menu section is implemented in the sidebar layout file (`resources/views/layouts/sidebar.blade.php`). It provides a hierarchical navigation structure with role-based access control, active state management, and collapsible dropdown menus.

## Menu Structure

The menu is organized into the following main sections:

### 1. Dashboard
- **Route**: `root`
- **Icon**: `ph-gauge` (Phosphor Icons)
- **Translation Key**: `translation.dashboard`
- **Access**: All authenticated users
- **Type**: Single link (no dropdown)

### 2. Blocks
- **Route Group**: `blocks.*`, `block-types.*`
- **Icon**: `ph-buildings` (Phosphor Icons)
- **Translation Key**: `translation.blocks`
- **Access**: All users **except** `Contractor Admin`
- **Type**: Collapsible dropdown menu
- **Submenu Items**:
  - **List Blocks**
    - Route: `blocks.index`
    - Translation: `translation.list-blocks`
    - Access: All users (except Contractor Admin)
  - **Create Block**
    - Route: `blocks.create`
    - Translation: `translation.create-block`
    - Access: `Admin`, `Super Admin`, `Property manager`, `Office Administrator` only
    - Blade Directive: `@hasAnyRole('Admin|Super Admin|Property manager|Office Administrator')`
  - **Block Types**
    - Route: `block-types.index`
    - Translation: `translation.block-types`
    - Access: Admin users only
    - Blade Directive: `@admin`

### 3. Work Orders
- **Route Group**: `block-work-orders.*`
- **Icon**: `ph-list-dashes` (Phosphor Icons)
- **Translation Key**: `translation.work-orders`
- **Access**: All authenticated users
- **Type**: Collapsible dropdown menu
- **Submenu Items**:
  - **Block Work Orders**
    - Route: `block-work-orders.index`
    - Translation: `translation.block-work-orders`
    - Access: All users
  - **Create Block Work Order**
    - Route: `block-work-orders.create`
    - Translation: `translation.create-block-work-order`
    - Access: All users **except** `Contractor Admin`

### 4. Site Visits
- **Route Group**: `block-visits.*`
- **Icon**: `ph-map-pin` (Phosphor Icons)
- **Translation Key**: `translation.site-visits`
- **Access**: All users **except** `Contractor Admin`
- **Type**: Single link (no dropdown)

### 5. Block Inspections
- **Route Group**: `block-inspections.*`
- **Icon**: `ph-clipboard-text` (Phosphor Icons)
- **Translation Key**: `translation.block-inspections`
- **Access**: All users **except** `Contractor Admin`
- **Type**: Single link (no dropdown)

### 6. Issues
- **Route Group**: `block-issues.*`, `issues.*`
- **Icon**: `ph-warning` (Phosphor Icons)
- **Translation Key**: `translation.issues`
- **Access**: All users **except** `Contractor Admin`
- **Type**: Collapsible dropdown menu
- **Submenu Items**:
  - **Block Issues**
    - Route: `block-issues.index`
    - Translation: `translation.block-issues`
    - Access: All users (except Contractor Admin)
  - **Create Block Issue**
    - Route: `block-issues.create`
    - Translation: `translation.create-block-issue`
    - Access: All users (except Contractor Admin)
  - **Note**: General Issues (`issues.*`) routes are hidden in the menu but tracked for active state

### 7. Users
- **Route Group**: `users.*`, `user-types.*`, `contract-companies.*`
- **Icon**: `ph-users` (Phosphor Icons)
- **Translation Key**: `translation.users`
- **Access**: **Super Admin only**
- **Type**: Collapsible dropdown menu
- **Blade Directive**: `@superAdmin`
- **Submenu Items**:
  - **List Users**
    - Route: `users.index`
    - Translation: `translation.list-users`
    - Access: Super Admin only
  - **Create User**
    - Route: `users.create`
    - Translation: `translation.create-user`
    - Access: Super Admin only
  - **User Types**
    - Route: `user-types.index`
    - Translation: `translation.user-types`
    - Access: Super Admin only
  - **Create User Type**
    - Route: `user-types.create`
    - Translation: `translation.create-user-type`
    - Access: Super Admin only
  - **Contract Companies**
    - Route: `contract-companies.index`
    - Translation: Hardcoded as "Contract Companies"
    - Access: Super Admin only

## Role-Based Access Control

### User Types and Access

The menu implements role-based access control using the following mechanisms:

1. **User Type Checks**: `auth()->user()->hasType('Contractor Admin')`
2. **Blade Directives**:
   - `@admin`: Checks if user is Admin or Super Admin
   - `@superAdmin`: Checks if user is Super Admin only
   - `@hasAnyRole('Role1|Role2')`: Checks if user has any of the specified roles
   - `@role('RoleName')`: Checks if user has a specific role type

### Access Matrix

| Menu Item | Contractor Admin | Other Users | Admin/Super Admin |
|-----------|-----------------|-------------|-------------------|
| Dashboard | ✅ | ✅ | ✅ |
| Blocks | ❌ | ✅ | ✅ |
| - List Blocks | ❌ | ✅ | ✅ |
| - Create Block | ❌ | ❌ | ✅* |
| - Block Types | ❌ | ❌ | ✅ |
| Work Orders | ✅ | ✅ | ✅ |
| - List Work Orders | ✅ | ✅ | ✅ |
| - Create Work Order | ❌ | ✅ | ✅ |
| Site Visits | ❌ | ✅ | ✅ |
| Block Inspections | ❌ | ✅ | ✅ |
| Issues | ❌ | ✅ | ✅ |
| Users | ❌ | ❌ | ✅* |
| - List Users | ❌ | ❌ | ✅* |
| - Create User | ❌ | ❌ | ✅* |
| - User Types | ❌ | ❌ | ✅* |
| - Create User Type | ❌ | ❌ | ✅* |
| - Contract Companies | ❌ | ❌ | ✅* |

*Users menu is only visible to Super Admin users.

*Create Block is also available to Property manager and Office Administrator roles.

## Helper Functions

The sidebar includes several PHP helper functions for menu state management:

### `isActiveRoute($routeName)`
- **Purpose**: Checks if the current route matches the given route name
- **Implementation**: Uses `request()->routeIs($routeName)`
- **Usage**: Determines if a menu item should be marked as active

### `hasActiveChild($routeNames)`
- **Purpose**: Checks if any of the child routes are currently active
- **Parameters**: Array of route name patterns (supports wildcards like `blocks.*`)
- **Usage**: Determines if a parent menu item should be expanded and marked active

### `getMenuClasses($routeName, $childRoutes)`
- **Purpose**: Generates CSS classes for menu links
- **Parameters**:
  - `$routeName`: Single route name (optional)
  - `$childRoutes`: Array of child route patterns (optional)
- **Returns**: String of CSS classes including:
  - `nav-link menu-link` (base classes)
  - `active` (if route or child route is active)
  - `collapsed` (if dropdown is not expanded)
- **Usage**: Applied to parent menu items

### `getDropdownClasses($childRoutes)`
- **Purpose**: Generates CSS classes for dropdown containers
- **Parameters**: Array of child route patterns
- **Returns**: String of CSS classes:
  - `collapse menu-dropdown` (base classes)
  - `show` (if any child route is active)
- **Usage**: Applied to dropdown div containers

### `getSubmenuClasses($routeName)`
- **Purpose**: Generates CSS classes for submenu links
- **Parameters**: Route name
- **Returns**: String of CSS classes:
  - `nav-link` (base class)
  - `active` (if route is active)
- **Usage**: Applied to submenu items

## Technical Implementation

### File Location
- **Main Menu File**: `resources/views/layouts/sidebar.blade.php`
- **Translation File**: `resources/lang/en/translation.php`
- **JavaScript Initialization**: `resources/js/app.js` (lines 830-864)

### JavaScript Active Menu Initialization

The menu includes JavaScript code (`initActiveMenu()`) that:
1. Detects the current page path
2. Adds `active` class to matching menu items
3. Expands parent dropdowns if child routes are active
4. Handles nested dropdown scenarios

### Icon System

The menu uses **Phosphor Icons** (Phosphor Icon library):
- Icon prefix: `ph-`
- Examples: `ph-gauge`, `ph-buildings`, `ph-list-dashes`, `ph-map-pin`, `ph-clipboard-text`, `ph-warning`, `ph-users`

### Logo Implementation

The sidebar includes a logo section with:
- **Dark Logo**: `absolute-sidebar-logo.svg` (for dark theme)
- **Light Logo**: `absolute-sidebar-logo-light.svg` (for light theme)
- **Icon Only**: `absolute-icon-only.svg` (for collapsed state)
- **Logo Versioning**: Uses file modification time for cache busting

### Bootstrap Integration

The menu uses Bootstrap 5 components:
- `navbar-nav` for the main menu container
- `nav-item` for menu items
- `nav-link` for links
- `collapse` for dropdown functionality
- `menu-dropdown` for dropdown containers

## Route Mappings

### Complete Route Reference

| Menu Item | Route Name | Controller Method |
|-----------|-----------|-------------------|
| Dashboard | `root` | `HomeController@root` |
| List Blocks | `blocks.index` | `BlockController@index` |
| Create Block | `blocks.create` | `BlockController@create` |
| Block Types | `block-types.index` | `BlockTypeController@index` |
| Block Work Orders | `block-work-orders.index` | `BlockWorkOrderController@index` |
| Create Work Order | `block-work-orders.create` | `BlockWorkOrderController@create` |
| Site Visits | `block-visits.index` | `BlockVisitController@index` |
| Block Inspections | `block-inspections.index` | `BlockInspectionController@index` |
| Block Issues | `block-issues.index` | `BlockIssueController@index` |
| Create Block Issue | `block-issues.create` | `BlockIssueController@create` |
| List Users | `users.index` | `UserController@index` |
| Create User | `users.create` | `UserController@create` |
| User Types | `user-types.index` | `UserTypeController@index` |
| Create User Type | `user-types.create` | `UserTypeController@create` |
| Contract Companies | `contract-companies.index` | `ContractCompanyController@index` |

## Translation Keys

All menu labels use Laravel's translation system with the `@lang()` directive. Translation keys are defined in `resources/lang/en/translation.php`:

- `translation.menu` - "Menu" (section title)
- `translation.dashboard` - "Dashboard"
- `translation.blocks` - "Blocks"
- `translation.list-blocks` - "List Blocks"
- `translation.create-block` - "Create Block"
- `translation.block-types` - "Block Types"
- `translation.work-orders` - "Work Orders"
- `translation.block-work-orders` - "Block Work Orders"
- `translation.create-block-work-order` - "Create Block Work Order"
- `translation.site-visits` - "Site Visits"
- `translation.block-inspections` - "Block Inspections"
- `translation.issues` - "Issues"
- `translation.block-issues` - "Block Issues"
- `translation.create-block-issue` - "Create Block Issue"
- `translation.users` - "Users"
- `translation.list-users` - "List Users"
- `translation.create-user` - "Create User"
- `translation.user-types` - "User Types"
- `translation.create-user-type` - "Create User Type"

## Adding New Menu Items

To add a new menu item:

1. **Add the route** in `routes/web.php`
2. **Add translation key** in `resources/lang/en/translation.php`
3. **Add menu item** in `resources/views/layouts/sidebar.blade.php`:
   ```blade
   <li class="nav-item">
       <a class="{{ getMenuClasses('route-name') }}" href="{{ route('route-name') }}">
           <i class="ph-icon-name"></i> <span>@lang('translation.key')</span>
       </a>
   </li>
   ```
4. **Apply role-based access** if needed using `@if`, `@admin`, `@superAdmin`, `@role`, or `@hasAnyRole` directives
5. **For dropdown menus**, wrap submenu items in a collapse div structure

## Notes

- The menu automatically handles active states based on current route
- Dropdown menus expand/collapse based on active child routes
- All menu items require authentication (wrapped in `auth` middleware)
- The menu is responsive and works with the sidebar collapse/expand functionality
- General Issues routes (`issues.*`) are tracked for active state but not displayed in the menu
