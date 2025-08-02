# PROMAN - Property Management System

A comprehensive property management system built with Laravel 10, designed to streamline property management operations.

## Features

- **Property Management**: Complete property listing and management
- **Tenant Management**: Tenant profiles, lease management, and communication
- **Financial Tracking**: Rent collection, expense tracking, and financial reporting
- **Maintenance Requests**: Work order management and maintenance tracking
- **Document Management**: Lease agreements, contracts, and document storage
- **Reporting & Analytics**: Comprehensive reporting and dashboard analytics
- **User Management**: Role-based access control and user administration

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Bootstrap 5, jQuery
- **Database**: MySQL/MariaDB
- **Development**: DDEV for local development

## Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- DDEV (for local development)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd proman
   ```

2. **Start DDEV**
   ```bash
   ddev start
   ```

3. **Install dependencies**
   ```bash
   ddev composer install
   ddev npm install
   ```

4. **Set up the database**
   ```bash
   ddev migrate-fresh-seed
   ```

5. **Build assets**
   ```bash
   ddev npm run build
   ```

6. **Access the application**
   - URL: https://proman.ddev.site
   - Admin Login: admin@proman.com / 12345678

## Development

### Database Commands

```bash
# Run migrations
ddev migrate

# Run seeders
ddev seed

# Fresh install with seeders
ddev migrate-fresh-seed
```

### Laravel Commands

```bash
# Run any Laravel command
ddev artisan [command]

# Examples:
ddev artisan route:list
ddev artisan make:controller PropertyController
ddev artisan tinker
```

### Asset Compilation

```bash
# Development
ddev npm run dev

# Production
ddev npm run build
```

## Project Structure

```
proman/
├── app/                    # Application logic
│   ├── Http/              # Controllers, Middleware
│   ├── Models/            # Eloquent models
│   └── Providers/         # Service providers
├── database/              # Migrations, seeders, factories
├── resources/             # Views, assets, language files
│   ├── views/             # Blade templates
│   ├── js/                # JavaScript files
│   └── scss/              # Stylesheets
├── routes/                # Route definitions
└── public/                # Public assets
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is proprietary software. All rights reserved.

## Support

For support and questions, please contact the development team.
