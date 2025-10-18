<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockInspectionAsset;
use App\Models\BlockInspectionAssetImage;

class BlockInspectionAssetImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates sample block inspection asset images for testing
     */
    public function run(): void
    {
        $this->command->info('Starting Block Inspection Asset Image seeding...');
        
        // Get all block inspection assets
        $blockInspectionAssets = BlockInspectionAsset::all();

        if ($blockInspectionAssets->isEmpty()) {
            $this->command->warn('No block inspection assets found. Skipping sample image creation.');
            $this->command->info('Please run BlockInspectionSeeder first to create inspection assets.');
            return;
        }

        $sampleImageNames = [
            'asset_overview.jpg',
            'asset_detail.jpg',
            'asset_condition.jpg',
            'damage_inspection.jpg',
            'wear_tear_check.jpg',
            'maintenance_status.jpg',
            'safety_inspection.jpg',
            'compliance_check.jpg',
            'before_repair.jpg',
            'after_repair.jpg',
            'close_up_view.jpg',
            'wide_angle_view.jpg',
            'installation_check.jpg',
            'functionality_test.jpg',
            'documentation_photo.jpg',
        ];

        $createdCount = 0;

        // Add 1-3 images to each block inspection asset
        foreach ($blockInspectionAssets as $asset) {
            // Skip if this asset already has images
            if ($asset->images()->count() > 0) {
                continue;
            }

            $numImages = rand(1, 3);
            
            for ($i = 0; $i < $numImages; $i++) {
                $imageName = $sampleImageNames[array_rand($sampleImageNames)];
                
                // Generate a unique timestamped filename
                $timestamp = now()->timestamp + $i;
                $randomString = substr(md5(uniqid()), 0, 8);
                $uniqueImageName = "{$timestamp}_{$randomString}_{$imageName}";
                
                // Path structure: inspection-assets/{block_inspection_id}/{block_inspection_asset_id}
                $imagePath = "inspection-assets/{$asset->block_inspection_id}/{$asset->id}";

                BlockInspectionAssetImage::create([
                    'block_inspection_asset_id' => $asset->id,
                    'block_inspection_id' => $asset->block_inspection_id,
                    'block_building_id' => $asset->block_building_id,
                    'building_asset_id' => $asset->building_asset_id,
                    'image_path' => $imagePath,
                    'image_name' => $uniqueImageName,
                    's3_status' => rand(0, 1), // Randomly mark some as uploaded to S3
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Successfully created {$createdCount} sample block inspection asset images.");
    }

    /**
     * Create images for a specific block inspection asset
     * Useful for targeted seeding
     */
    public function createImagesForAsset(int $blockInspectionAssetId, int $count = 3): void
    {
        $asset = BlockInspectionAsset::find($blockInspectionAssetId);

        if (!$asset) {
            $this->command->error("Block inspection asset with ID {$blockInspectionAssetId} not found.");
            return;
        }

        $sampleImageNames = [
            'initial_inspection.jpg',
            'detailed_view.jpg',
            'status_documentation.jpg',
            'compliance_photo.jpg',
            'inspection_evidence.jpg',
        ];

        $imagePath = "inspection-assets/{$asset->block_inspection_id}/{$asset->id}";

        for ($i = 0; $i < $count; $i++) {
            $imageName = $sampleImageNames[$i % count($sampleImageNames)];
            $timestamp = now()->timestamp + $i;
            $randomString = substr(md5(uniqid()), 0, 8);
            $uniqueImageName = "{$timestamp}_{$randomString}_{$imageName}";

            BlockInspectionAssetImage::create([
                'block_inspection_asset_id' => $asset->id,
                'block_inspection_id' => $asset->block_inspection_id,
                'block_building_id' => $asset->block_building_id,
                'building_asset_id' => $asset->building_asset_id,
                'image_path' => $imagePath,
                'image_name' => $uniqueImageName,
                's3_status' => 0,
            ]);
        }

        $this->command->info("Created {$count} images for block inspection asset ID {$blockInspectionAssetId}.");
    }
}

