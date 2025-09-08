<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\BlockBuilding;
use App\Models\BlockUnitType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockUnit>
 */
class BlockUnitFactory extends Factory
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
            'block_building_id' => BlockBuilding::factory(),
            'block_unit_type_id' => BlockUnitType::factory(),
            'unit_code' => $this->faker->unique()->numerify('###'),
            'unit_name' => $this->faker->randomElement(['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E']),
            'owners_name' => $this->faker->name(),
            'salutation' => $this->faker->randomElement(['Mr.', 'Ms.', 'Dr.', 'Prof.']),
            'email' => $this->faker->email(),
            'resident' => $this->faker->boolean(),
            'address1' => $this->faker->streetAddress(),
            'address2' => $this->faker->secondaryAddress(),
            'address3' => $this->faker->city(),
            'country_id' => 1,
            'state_id' => 1,
            'zip' => $this->faker->postcode(),
            'mobile_no' => $this->faker->phoneNumber(),
            'phone_number' => $this->faker->phoneNumber(),
            'letting_agent' => $this->faker->company(),
            'misc_info' => $this->faker->paragraph(),
            'created_by' => User::factory(),
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }
}
