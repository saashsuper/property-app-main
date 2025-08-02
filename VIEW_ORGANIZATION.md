# PROMAN View Organization

## Overview
The views have been reorganized into logical folders for better maintainability and structure.

## Directory Structure

```
resources/views/
├── layouts/                    # Main layout templates
│   ├── master.blade.php       # Primary layout
│   ├── master-without-nav.blade.php
│   ├── sidebar.blade.php      # Sidebar component
│   ├── topbar.blade.php       # Top navigation
│   ├── footer.blade.php       # Footer component
│   ├── customizer.blade.php   # Theme customizer
│   ├── head-css.blade.php     # CSS includes
│   ├── vendor-scripts.blade.php # JavaScript includes
│   └── variations/            # Layout variations
│       ├── layouts-detached.blade.php
│       ├── layouts-horizontal.blade.php
│       ├── layouts-two-column.blade.php
│       └── layouts-vertical-hovered.blade.php
│
├── auth/                      # Authentication views
│   ├── login.blade.php        # Login page
│   ├── register.blade.php     # Registration page
│   ├── verify.blade.php       # Email verification
│   ├── passwords/             # Password management
│   │   ├── email.blade.php    # Password reset email
│   │   ├── reset.blade.php    # Password reset form
│   │   └── confirm.blade.php  # Password confirmation
│   └── legacy/                # Old auth views (deprecated)
│       ├── auth-signin.blade.php
│       ├── auth-signup.blade.php
│       ├── auth-logout.blade.php
│       ├── auth-lockscreen.blade.php
│       ├── auth-pass-reset.blade.php
│       ├── auth-pass-change.blade.php
│       ├── auth-success-msg.blade.php
│       ├── auth-offline.blade.php
│       ├── auth-twostep.blade.php
│       ├── auth-404.blade.php
│       ├── auth-500.blade.php
│       └── auth-503.blade.php
│
├── dashboard/                 # Main application views
│   └── index.blade.php        # Dashboard home page
│
├── pages/                     # General pages
│   ├── maintenance/           # Maintenance pages
│   │   └── pages-maintenance.blade.php
│   ├── coming-soon/           # Coming soon pages
│   │   └── pages-coming-soon.blade.php
│   └── starter/               # Starter pages
│       └── pages-starter.blade.php
│
├── errors/                    # Error pages
│   ├── 404.blade.php          # Not found
│   ├── 500.blade.php          # Server error
│   ├── 401.blade.php          # Unauthorized
│   ├── 403.blade.php          # Forbidden
│   ├── 419.blade.php          # Page expired
│   ├── 429.blade.php          # Too many requests
│   ├── 402.blade.php          # Payment required
│   ├── 503.blade.php          # Service unavailable
│   ├── layout.blade.php       # Error layout
│   └── minimal.blade.php      # Minimal error layout
│
└── components/                # Reusable components
    └── breadcrumb.blade.php   # Breadcrumb component
```

## Organization Principles

### 1. **Layouts** (`/layouts`)
- **Purpose**: Base templates and layout components
- **Contains**: Master layouts, navigation, footer, CSS/JS includes
- **Variations**: Different layout styles in `/variations` subfolder

### 2. **Authentication** (`/auth`)
- **Purpose**: User authentication and authorization
- **Structure**: 
  - Main auth views (login, register, verify)
  - Password management in `/passwords` subfolder
  - Legacy views in `/legacy` subfolder (for backward compatibility)

### 3. **Dashboard** (`/dashboard`)
- **Purpose**: Main application interface
- **Contains**: Dashboard pages, main app views

### 4. **Pages** (`/pages`)
- **Purpose**: General application pages
- **Subfolders**: Organized by page type (maintenance, coming-soon, starter)

### 5. **Errors** (`/errors`)
- **Purpose**: Error handling pages
- **Contains**: All HTTP error pages and error layouts

### 6. **Components** (`/components`)
- **Purpose**: Reusable UI components
- **Contains**: Modular components like breadcrumbs, forms, etc.

## Benefits of This Organization

1. **Logical Grouping**: Related views are grouped together
2. **Easy Navigation**: Clear folder structure makes finding files easier
3. **Scalability**: Easy to add new views in appropriate folders
4. **Maintainability**: Separates concerns and reduces clutter
5. **Team Collaboration**: Clear structure helps team members understand the codebase

## Migration Notes

### Files Moved:
- **Legacy Auth Views**: Moved from root to `/auth/legacy/`
- **Page Views**: Moved from root to `/pages/{category}/`
- **Layout Variations**: Moved from root to `/layouts/variations/`
- **Dashboard Views**: Moved from root to `/dashboard/`

### Backward Compatibility:
- Legacy auth views are preserved in `/auth/legacy/` for reference
- All existing functionality remains intact
- Routes and controllers don't need changes

## Future Considerations

1. **Property Management Views**: Add `/properties/` folder for property-related views
2. **Tenant Management**: Add `/tenants/` folder for tenant management views
3. **Financial Views**: Add `/financial/` folder for billing and financial views
4. **Reports**: Add `/reports/` folder for reporting and analytics views
5. **Settings**: Add `/settings/` folder for application settings views

## Usage Examples

### Including Views:
```php
// Dashboard view
return view('dashboard.index');

// Auth view
return view('auth.login');

// Error view
return view('errors.404');

// Page view
return view('pages.maintenance.pages-maintenance');
```

### Extending Layouts:
```php
@extends('layouts.master')

@section('content')
    // Your content here
@endsection
```

This organization provides a solid foundation for the PROMAN property management system!
