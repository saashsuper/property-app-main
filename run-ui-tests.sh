#!/bin/bash

echo "🚀 Running PROMAN UI Test Suite"
echo "================================"

# Clear caches before running tests
echo "📝 Clearing caches..."
ddev exec php artisan view:clear
ddev exec php artisan config:clear
ddev exec php artisan route:clear

echo ""
echo "🧪 Running UI Validation Tests..."
echo "--------------------------------"

# Test 1: Login Page
echo "1️⃣ Testing Login Page..."
ddev exec php artisan test --filter=test_login_page_contains_required_elements

# Test 2: Dashboard Page  
echo "2️⃣ Testing Dashboard Page..."
ddev exec php artisan test --filter=test_dashboard_page_contains_required_elements

# Test 3: Users Page
echo "3️⃣ Testing Users Page..."
ddev exec php artisan test --filter=test_users_page_contains_required_elements

# Test 4: Block Inspections Page
echo "4️⃣ Testing Block Inspections Page..."
ddev exec php artisan test --filter=test_block_inspections_page_contains_required_elements

# Test 5: Work Orders Page
echo "5️⃣ Testing Work Orders Page..."
ddev exec php artisan test --filter=test_work_orders_page_contains_required_elements

# Test 6: Blocks Page
echo "6️⃣ Testing Blocks Page..."
ddev exec php artisan test --filter=test_blocks_page_contains_required_elements

echo ""
echo "✅ UI Test Suite Complete!"
echo "================================"
