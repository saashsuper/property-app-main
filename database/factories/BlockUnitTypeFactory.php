<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockUnitType>
 */
class BlockUnitTypeFactory extends Factory
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
                'Studio',
                '1 Bedroom',
                '2 Bedroom',
                '3 Bedroom',
                '4 Bedroom',
                'Penthouse',
                'Duplex',
                'Townhouse'
            ]),
        ];
    }
}
