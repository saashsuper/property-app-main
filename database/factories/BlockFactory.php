<?php

namespace Database\Factories;

use App\Models\BlockType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company() . ' Block',
            'management_company' => $this->faker->company(),
            'block_type_id' => BlockType::factory(),
            'user_id' => User::factory(),
            'block_manager_id' => User::factory(),
            'address1' => $this->faker->streetAddress(),
            'address2' => $this->faker->secondaryAddress(),
            'address3' => $this->faker->city(),
            'block_address' => $this->faker->address(),
            'management_company_address' => $this->faker->address(),
            'country_id' => 1,
            'state_id' => 1,
            'car_spaces' => $this->faker->numberBetween(10, 100),
            'inspection_count' => $this->faker->numberBetween(0, 50),
            'no_of_units' => $this->faker->numberBetween(20, 200),
            'created_by' => User::factory(),
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}
