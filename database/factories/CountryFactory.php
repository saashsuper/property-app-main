<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_code' => $this->faker->countryCode(),
            'country_name' => $this->faker->country(),
            'phonecode' => $this->faker->numberBetween(1, 999),
        ];
    }
}
