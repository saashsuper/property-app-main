<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockBuildingType>
 */
class BlockBuildingTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Residential Tower',
                'Commercial Building',
                'Mixed Use',
                'Parking Structure',
                'Amenity Building',
                'Service Building'
            ]),
        ];
    }
}
