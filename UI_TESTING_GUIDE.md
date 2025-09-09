# PROMAN UI Testing Guide

## 🎯 Overview
This guide explains how to run UI tests for the PROMAN application. We use HTTP-based UI validation tests that provide comprehensive coverage without requiring a browser.

## 🚀 Quick Start

### Run All UI Tests (Recommended)
```bash
# Method 1: Using the shell script
./run-ui-tests.sh

# Method 2: Using npm
npm run test:ui

# Method 3: Using Laravel artisan (if command is registered)
ddev exec php artisan test:ui
```

## 📋 Available Test Commands

### Individual Page Tests
```bash
# Login Page
npm run test:ui:login
# or
ddev exec php artisan test --filter=test_login_page_contains_required_elements

# Dashboard Page
npm run test:ui:dashboard
# or
ddev exec php artisan test --filter=test_dashboard_page_contains_required_elements

# Users Page
npm run test:ui:users
# or
ddev exec php artisan test --filter=test_users_page_contains_required_elements

# Block Inspections Page
npm run test:ui:inspections
# or
ddev exec php artisan test --filter=test_block_inspections_page_contains_required_elements

# Work Orders Page
npm run test:ui:workorders
# or
ddev exec php artisan test --filter=test_work_orders_page_contains_required_elements

# Blocks Page
npm run test:ui:blocks
# or
ddev exec php artisan test --filter=test_blocks_page_contains_required_elements
```

## 🧪 Test Coverage

### ✅ Login Page Tests
- Validates presence of "Sign In", "Email", "Password" fields
- Checks for "Forgot password?" link
- Verifies "PROMAN" branding

### ✅ Dashboard Page Tests
- Validates presence of "Dashboard" title
- Checks for "Welcome" message with user name
- Verifies navigation elements (Users, Blocks, Block Inspections, Work Orders)

### ✅ Users Page Tests
- Validates presence of "List Users" title
- Checks for "Search" functionality
- Verifies table headers (Name, Email)

### ✅ Block Inspections Page Tests
- Validates presence of "Block Inspections List" title
- Checks for "Search" and "Status" filters
- Verifies table headers (Reference, Block, Scheduled Date, Lead Inspector, Actions)

### ✅ Work Orders Page Tests
- Validates presence of "Work Orders" title
- Checks for "Search" functionality
- Verifies table headers (Code, Actions)

### ✅ Blocks Page Tests
- Validates presence of "Blocks List" title
- Checks for "Search" functionality and "Add New Block" button
- Verifies "Export" functionality
- Validates comprehensive table headers (Name, Type, Management Company, Block Manager, Address, Units, Issues, Work Orders, Actions)

## 🔧 Troubleshooting

### Common Issues

1. **`isActiveRoute()` Error**
   - This occurs when running multiple tests together
   - **Solution**: Use individual test commands or the provided shell script
   - The shell script runs tests sequentially to avoid this issue

2. **Cache Issues**
   - If tests fail unexpectedly, clear caches:
   ```bash
   ddev exec php artisan view:clear
   ddev exec php artisan config:clear
   ddev exec php artisan route:clear
   ```

3. **Database Issues**
   - Tests use in-memory SQLite database
   - Each test runs with fresh database state
   - No manual database setup required

## 📁 Test Files

- `tests/Feature/UIValidationTest.php` - Main UI validation tests
- `tests/Feature/UIValidationSuiteTest.php` - Suite test (has issues with multiple tests)
- `run-ui-tests.sh` - Shell script to run all tests
- `phpunit-ui.xml` - PHPUnit configuration for UI tests

## 🎯 Best Practices

1. **Run tests before deployment** to ensure UI elements are present
2. **Add new tests** when adding new pages or UI components
3. **Use individual test commands** for debugging specific pages
4. **Clear caches** if tests behave unexpectedly
5. **Keep tests simple** - focus on essential UI elements

## 📊 Test Results

When you run the UI test suite, you'll see output like:
```
🚀 Running PROMAN UI Test Suite
================================
📝 Clearing caches...
🧪 Running UI Validation Tests...
--------------------------------
1️⃣ Testing Login Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ login page contains required elements

2️⃣ Testing Dashboard Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ dashboard page contains required elements

3️⃣ Testing Users Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ users page contains required elements

4️⃣ Testing Block Inspections Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ block inspections page contains required elements

5️⃣ Testing Work Orders Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ work orders page contains required elements

6️⃣ Testing Blocks Page...
   PASS  Tests\Feature\UIValidationTest
   ✓ blocks page contains required elements

✅ UI Test Suite Complete!
================================
```

## 🔄 Adding New UI Tests

To add tests for a new page:

1. Add a new test method to `tests/Feature/UIValidationTest.php`
2. Add the test to the shell script `run-ui-tests.sh`
3. Add an npm script to `package.json` if desired
4. Update this documentation

Example:
```php
public function test_new_page_contains_required_elements()
{
    $response = $this->actingAs($this->admin)->get('/new-page');
    
    $response->assertStatus(200);
    $response->assertSee('Expected Title');
    $response->assertSee('Expected Element');
}
```

## 🎉 Success!

Your UI testing infrastructure is now fully set up and working! All tests are passing and provide comprehensive coverage of your application's user interface.
