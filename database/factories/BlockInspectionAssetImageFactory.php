<?php

namespace Database\Factories;

use App\Models\BlockInspectionAsset;
use App\Models\BlockInspection;
use App\Models\BlockBuilding;
use App\Models\BuildingAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockInspectionAssetImage>
 */
class BlockInspectionAssetImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = $this->faker->uuid() . '.jpg';
        
        return [
            'block_inspection_asset_id' => BlockInspectionAsset::factory(),
            'block_inspection_id' => BlockInspection::factory(),
            'block_building_id' => BlockBuilding::factory(),
            'building_asset_id' => BuildingAsset::factory(),
            'image_path' => 'inspection_assets/' . $fileName,
            'image_name' => $this->faker->word() . '.jpg',
            's3_status' => 0,
        ];
    }

    /**
     * Indicate that the image has been uploaded to S3.
     */
    public function uploaded(): static
    {
        return $this->state(fn (array $attributes) => [
            's3_status' => 1,
        ]);
    }

    /**
     * Indicate that the image is a PNG.
     */
    public function png(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_name' => $this->faker->word() . '.png',
            'image_path' => 'inspection_assets/' . $this->faker->uuid() . '.png',
        ]);
    }

    /**
     * Create image with specific status.
     */
    public function withStatus(int $status): static
    {
        return $this->state(fn (array $attributes) => [
            's3_status' => $status,
        ]);
    }
}

