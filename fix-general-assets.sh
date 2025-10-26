#!/bin/bash

# Fix General Assets - Migration Script
# This script runs the necessary migrations to fix the general assets insertion issue

echo "======================================"
echo "General Assets Fix - Migration Script"
echo "======================================"
echo ""

echo "Step 1: Running migrations..."
ddev exec php artisan migrate

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Migrations completed successfully!"
    echo ""
    echo "Step 2: Clearing application cache..."
    ddev exec php artisan cache:clear
    
    echo ""
    echo "======================================"
    echo "✅ Fix applied successfully!"
    echo "======================================"
    echo ""
    echo "Next steps:"
    echo "1. Visit: https://proman.ddev.site/block-inspections/12/edit"
    echo "2. Expand 'GENERAL ASSETS' section"
    echo "3. Select status for any asset and add notes/photos"
    echo "4. Click 'Update Inspection'"
    echo ""
    echo "The general assets data should now save to the database!"
    echo ""
else
    echo ""
    echo "❌ Migration failed!"
    echo ""
    echo "Please check the error message above and:"
    echo "1. Make sure DDEV is running (ddev status)"
    echo "2. Try running manually: ddev exec php artisan migrate"
    echo "3. Check the database connection in .env"
    echo ""
fi

