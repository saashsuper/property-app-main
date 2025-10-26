# Block Building Assets - Quick Reference Guide

## Overview
A pivot table linking blocks and building assets in a many-to-many relationship.

## Quick Start

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Run Seeder
```bash
php artisan db:seed --class=BlockBuildingAssetSeeder
```

## Common Operations

### Get Assets for a Block
```php
$block = Block::find(1);
$assets = $block->buildingAssets;
```

### Get Blocks for an Asset
```php
$asset = BuildingAsset::find(1);
$blocks = $asset->blocks;
```

### Assign Assets to Block (Replace all)
```php
$block->buildingAssets()->sync([1, 2, 3, 7]);
```

### Add Assets (Keep existing)
```php
$block->buildingAssets()->attach([9, 10]);
```

### Remove Assets
```php
$block->buildingAssets()->detach([9]);
```

### Check if Block has Asset
```php
$hasAsset = $block->buildingAssets->contains('id', 17);
```

## Files Created

1. **Migration**: `database/migrations/2025_10_26_090317_create_block_building_assets_table.php`
2. **Model**: `app/Models/BlockBuildingAsset.php`
3. **Seeder**: `database/seeders/BlockBuildingAssetSeeder.php`

## Model Updates

- **Block.php**: Added `buildingAssets()` and `blockBuildingAssets()` methods
- **BuildingAsset.php**: Added `blocks()` and `blockBuildingAssets()` methods

## Asset Categories

- **Structural** (1-10): Foundation, Walls, Roof, Windows, Doors, etc.
- **Electrical** (11-18): Panels, Wiring, Lighting, Fire Alarms, etc.
- **Plumbing** (19-26): Water Supply, Drainage, Heaters, Pipes, etc.
- **HVAC** (27-31): AC, Heating, Ventilation, Filters, etc.
- **Safety** (32-38): Fire Extinguishers, Smoke Detectors, Emergency Exits, etc.
- **Common Areas** (39-45): Lobby, Corridors, Parking, Gym, etc.
- **Compliance** (46-50): Permits, Certificates, Insurance, Records, etc.

## Seeder Logic

The seeder assigns assets based on block type:
- **All Blocks**: Get default structural, electrical, safety, and common area assets
- **Multi-level/Duplex (Types 1-2)**: Additional elevators, balconies, HVAC
- **Commercial (Type 4)**: Additional commercial equipment and safety items

## For More Details

See: `BLOCK_BUILDING_ASSETS_IMPLEMENTATION.md`

