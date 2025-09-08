<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\BlockBuildingType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockBuilding>
 */
class BlockBuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'block_id' => Block::factory(),
            'building_type_id' => BlockBuildingType::factory(),
            'name' => $this->faker->randomElement(['Building A', 'Building B', 'Building C', 'Tower 1', 'Tower 2']),
            'floor_no' => $this->faker->numberBetween(1, 20),
            'roof_type' => $this->faker->randomElement(['Flat', 'Sloped', 'Dome', 'Gable']),
            'no_lift' => $this->faker->numberBetween(1, 4),
        ];
    }
}
