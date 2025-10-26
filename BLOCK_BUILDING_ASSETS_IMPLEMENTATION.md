# Block Building Assets Implementation

## Overview
This document describes the implementation of the `block_building_assets` pivot table, which establishes a many-to-many relationship between blocks and building assets. This allows each block to be associated with multiple building assets and vice versa.

## Database Structure

### Table: `block_building_assets`
Based on the analysis of the `building_assets` table from `saashmagna.sql`, a new pivot table has been created.

**Schema:**
```
- id (bigint, auto-increment, primary key)
- block_id (unsignedInteger, foreign key to blocks.id)
- building_asset_id (unsignedSmallInteger, foreign key to building_assets.id)
- created_at (timestamp)
- updated_at (timestamp)
```

**Constraints:**
- Foreign key on `block_id` references `blocks.id` with cascade on delete
- Foreign key on `building_asset_id` references `building_assets.id` with cascade on delete
- Unique constraint on `[block_id, building_asset_id]` to prevent duplicate entries

## Files Created

### 1. Migration
**File:** `database/migrations/2025_10_26_090317_create_block_building_assets_table.php`

Creates the `block_building_assets` table with proper foreign key constraints and unique index.

### 2. Model
**File:** `app/Models/BlockBuildingAsset.php`

**Features:**
- Mass assignable fields: `block_id`, `building_asset_id`
- Relationships:
  - `block()` - belongsTo Block
  - `buildingAsset()` - belongsTo BuildingAsset

### 3. Seeder
**File:** `database/seeders/BlockBuildingAssetSeeder.php`

**Features:**
- Assigns building assets to blocks based on block type
- Default assets (common to all blocks):
  - Foundation, Walls, Roof, Windows, Doors, Stairs
  - Electrical: Main Panel, Wiring, Lighting Systems, Emergency Lighting
  - Safety: Fire Alarm, Security System, Fire Extinguishers, Smoke Detectors, Emergency Exits
  - Plumbing: Water Supply, Drainage System
  - Common Areas: Lobby, Corridors, Parking Area

- Multi-level apartments and duplexes (block_type_id: 1, 2) also get:
  - Elevators, Balconies
  - HVAC: Air Conditioning, Heating System, Ventilation
  - Garden/Landscaping

- Commercial buildings (block_type_id: 4) also get:
  - Power Outlets, Circuit Breakers
  - Water Heaters, Pipes
  - Air Filters, Ductwork
  - Carbon Monoxide Detectors, Fire Sprinklers
  - Handrails, Safety Signs

## Model Relationships Updated

### Block Model (`app/Models/Block.php`)
Added relationships:
```php
// Get building assets for the block (many-to-many)
public function buildingAssets()
{
    return $this->belongsToMany(BuildingAsset::class, 'block_building_assets', 'block_id', 'building_asset_id')
                ->withTimestamps();
}

// Get block building assets (pivot records)
public function blockBuildingAssets()
{
    return $this->hasMany(BlockBuildingAsset::class);
}
```

### BuildingAsset Model (`app/Models/BuildingAsset.php`)
Added relationships:
```php
// Get blocks that have this building asset (many-to-many)
public function blocks()
{
    return $this->belongsToMany(Block::class, 'block_building_assets', 'building_asset_id', 'block_id')
                ->withTimestamps();
}

// Get block building assets (pivot records)
public function blockBuildingAssets()
{
    return $this->hasMany(BlockBuildingAsset::class);
}
```

## Usage Examples

### Running the Migration
```bash
php artisan migrate
```

### Running the Seeder
```bash
php artisan db:seed --class=BlockBuildingAssetSeeder
```

### Code Examples

#### 1. Get all building assets for a block
```php
$block = Block::find(1);
$assets = $block->buildingAssets;

// Or with value types
$assets = $block->buildingAssets()->with('valueType')->get();
```

#### 2. Get all blocks that have a specific building asset
```php
$buildingAsset = BuildingAsset::find(1); // Foundation
$blocks = $buildingAsset->blocks;
```

#### 3. Assign building assets to a block
```php
$block = Block::find(1);
$assetIds = [1, 2, 3, 7, 11, 17]; // Foundation, Walls, Roof, Doors, Main Electrical Panel, Fire Alarm
$block->buildingAssets()->sync($assetIds);
```

#### 4. Add building assets to a block (without removing existing)
```php
$block = Block::find(1);
$block->buildingAssets()->attach([9, 10]); // Add Elevators and Balconies
```

#### 5. Remove building assets from a block
```php
$block = Block::find(1);
$block->buildingAssets()->detach([9]); // Remove Elevators
```

#### 6. Check if a block has a specific building asset
```php
$block = Block::find(1);
$hasFireAlarm = $block->buildingAssets->contains('id', 17);

// Or using query
$hasFireAlarm = $block->buildingAssets()->where('building_asset_id', 17)->exists();
```

#### 7. Count building assets for a block
```php
$block = Block::find(1);
$assetCount = $block->buildingAssets()->count();
```

#### 8. Get blocks with specific building assets
```php
// Blocks that have Fire Alarm System (id: 17)
$blocks = Block::whereHas('buildingAssets', function($query) {
    $query->where('building_asset_id', 17);
})->get();
```

#### 9. Get building assets grouped by value type
```php
$block = Block::find(1);
$assetsByType = $block->buildingAssets()
    ->with('valueType')
    ->get()
    ->groupBy('valueType.name');
```

#### 10. Bulk operations
```php
// Assign all safety equipment to a block
$safetyAssetIds = BuildingAsset::whereIn('id', [32, 33, 34, 35, 36, 37, 38])->pluck('id');
$block->buildingAssets()->syncWithoutDetaching($safetyAssetIds);
```

## Building Asset Categories (from BuildingAssetSeeder)

### Structural Components (IDs: 1-10)
- Foundation, Walls, Roof, Ceiling, Floor
- Windows, Doors, Stairs, Elevators, Balconies

### Electrical Systems (IDs: 11-18)
- Main Electrical Panel, Electrical Wiring
- Lighting Systems, Emergency Lighting, Power Outlets
- Circuit Breakers, Fire Alarm System, Security System

### Plumbing Systems (IDs: 19-26)
- Water Supply, Drainage System, Water Heaters
- Pipes, Faucets, Toilets, Sinks, Showers

### HVAC Systems (IDs: 27-31)
- Air Conditioning, Heating System, Ventilation
- Air Filters, Ductwork

### Safety Equipment (IDs: 32-38)
- Fire Extinguishers, Smoke Detectors
- Carbon Monoxide Detectors, Emergency Exits
- Fire Sprinklers, Handrails, Safety Signs

### Common Areas (IDs: 39-45)
- Lobby, Corridors, Parking Area
- Garden/Landscaping, Pool, Gym, Playground

### Compliance Items (IDs: 46-50)
- Building Permits, Safety Certificates
- Insurance Documents, Maintenance Records, Inspection Reports

## Block Inspection Value Types

The building assets use different value types for inspection:

1. **Type 1 (Condition)**: Yes/No - Used for structural components and common areas
2. **Type 2 (Status)**: Working/Not Working - Used for electrical and plumbing systems
3. **Type 3**: Good/Avg/Poor
4. **Type 4 (Compliance)**: Yes/No/Pending - Used for compliance items
5. **Type 5 (Safety)**: Working/Not Working/Needs Attention - Used for safety equipment

## Database Seeder Integration

To include this seeder in your main `DatabaseSeeder.php`, add:

```php
public function run()
{
    // ... other seeders
    $this->call([
        BlockSeeder::class,
        BuildingAssetSeeder::class,
        BlockBuildingAssetSeeder::class, // Add this line
    ]);
}
```

## Notes

- The seeder will automatically skip if no blocks or building assets exist in the database
- The unique constraint prevents duplicate block-asset associations
- All timestamps are automatically managed by Laravel
- The seeder uses bulk inserts for better performance
- Assets are assigned based on block type to provide appropriate defaults
- You can modify asset assignments through the application UI after seeding

## Related Tables

- `blocks` - Main blocks table
- `building_assets` - Building assets master table
- `block_inspection_value_types` - Value types for building asset inspections
- `block_inspections` - Block inspection records
- `block_inspection_assets` - Individual asset inspection records

## Future Enhancements

Potential enhancements could include:
1. Custom asset selection during block creation
2. Asset condition tracking over time
3. Asset maintenance schedules
4. Asset replacement recommendations
5. Asset cost tracking
6. Asset warranty information
7. Asset vendor/supplier information

