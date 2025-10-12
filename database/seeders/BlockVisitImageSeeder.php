<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BlockVisit;
use App\Models\BlockVisitImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BlockVisitImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder:
     * 1. Updates existing block_visit_images records to populate the new image_name column
     * 2. Creates sample block visit images for testing
     */
    public function run(): void
    {
        // Step 1: Update existing records to extract image_name from image_path
        $this->updateExistingRecords();
        
        // Step 2: Create sample block visit images with the new structure
        $this->createSampleImages();
    }

    /**
     * Update existing block_visit_images records to populate image_name field
     */
    private function updateExistingRecords(): void
    {
        $existingImages = BlockVisitImage::whereNull('image_name')
            ->whereNotNull('image_path')
            ->get();

        if ($existingImages->isEmpty()) {
            $this->command->info('No existing block visit images found to update.');
            return;
        }

        $updatedCount = 0;

        foreach ($existingImages as $image) {
            // Extract filename from path
            // Old format: path might be "block-visits/123/image.jpg" (full path with filename)
            // New format: path is "block-visits/123" and filename is "image.jpg"
            
            $imageName = basename($image->image_path);
            
            // Only update if we extracted a valid filename
            if ($imageName && $imageName !== $image->image_path) {
                // Update path to directory only and set image_name
                $directoryPath = dirname($image->image_path);
                
                $image->update([
                    'image_path' => $directoryPath,
                    'image_name' => $imageName
                ]);
                
                $updatedCount++;
            }
        }

        $this->command->info("Updated {$updatedCount} existing block visit image records.");
    }

    /**
     * Create sample block visit images for testing
     */
    private function createSampleImages(): void
    {
        $blockVisits = BlockVisit::all();

        if ($blockVisits->isEmpty()) {
            $this->command->warn('No block visits found. Skipping sample image creation.');
            return;
        }

        $sampleImageNames = [
            'entrance_view.jpg',
            'lobby_area.jpg',
            'exterior_front.jpg',
            'exterior_back.jpg',
            'parking_lot.jpg',
            'common_area.jpg',
            'roof_inspection.jpg',
            'basement_check.jpg',
            'hvac_system.jpg',
            'electrical_panel.jpg',
            'plumbing_overview.jpg',
            'fire_safety_equipment.jpg',
            'emergency_exits.jpg',
            'elevator_inspection.jpg',
            'stairwell_condition.jpg',
            'facade_inspection.jpg',
            'drainage_system.jpg',
            'security_systems.jpg',
            'lighting_overview.jpg',
            'landscaping_view.jpg'
        ];

        $createdCount = 0;

        // Add 1-3 images to each block visit that doesn't have images yet
        foreach ($blockVisits as $visit) {
            // Skip if this visit already has images
            if ($visit->images()->count() > 0) {
                continue;
            }

            $numImages = rand(1, 3);
            
            for ($i = 0; $i < $numImages; $i++) {
                $imageName = $sampleImageNames[array_rand($sampleImageNames)];
                
                // Generate a unique timestamped filename
                $timestamp = now()->timestamp;
                $randomString = substr(md5(uniqid()), 0, 8);
                $uniqueImageName = "{$timestamp}_{$randomString}_{$imageName}";
                
                // Path structure: block-visits/{block_visit_id}
                $imagePath = "block-visits/{$visit->id}";

                BlockVisitImage::create([
                    'block_visit_id' => $visit->id,
                    'image_path' => $imagePath,
                    'image_name' => $uniqueImageName,
                    's3_status' => 0, // Not uploaded to S3 yet
                ]);

                $createdCount++;
            }
        }

        $this->command->info("Created {$createdCount} sample block visit images.");
    }

    /**
     * Alternative method: Create images for specific block visits
     * Useful for targeted seeding
     */
    public function createImagesForVisit(int $blockVisitId, int $count = 3): void
    {
        $visit = BlockVisit::find($blockVisitId);

        if (!$visit) {
            $this->command->error("Block visit with ID {$blockVisitId} not found.");
            return;
        }

        $sampleImageNames = [
            'site_overview.jpg',
            'detailed_inspection.jpg',
            'issue_documentation.jpg',
            'compliance_check.jpg',
            'safety_inspection.jpg',
        ];

        $imagePath = "block-visits/{$visit->id}";

        for ($i = 0; $i < $count; $i++) {
            $imageName = $sampleImageNames[$i % count($sampleImageNames)];
            $timestamp = now()->timestamp + $i;
            $randomString = substr(md5(uniqid()), 0, 8);
            $uniqueImageName = "{$timestamp}_{$randomString}_{$imageName}";

            BlockVisitImage::create([
                'block_visit_id' => $visit->id,
                'image_path' => $imagePath,
                'image_name' => $uniqueImageName,
                's3_status' => 0,
            ]);
        }

        $this->command->info("Created {$count} images for block visit ID {$blockVisitId}.");
    }
}

