<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $blockTypes = BlockType::all();
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $blocks = [
            [
                'name' => 'Sunset Towers',
                'management_company' => 'Metro Property Management',
                'block_type_id' => $blockTypes->where('name', 'Residential Apartment')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '123 Sunset Boulevard',
                'address2' => 'Suite 100',
                'address3' => 'Los Angeles, CA 90210',
                'country_id' => 1,
                'state_id' => 5,
                'car_spaces' => 150,
                'inspection_count' => 3,
                'no_of_units' => 120,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Downtown Plaza',
                'management_company' => 'Urban Real Estate Group',
                'block_type_id' => $blockTypes->where('name', 'Commercial Building')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '456 Main Street',
                'address2' => 'Floor 15',
                'address3' => 'New York, NY 10001',
                'country_id' => 1,
                'state_id' => 32,
                'car_spaces' => 75,
                'inspection_count' => 2,
                'no_of_units' => 45,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Riverside Gardens',
                'management_company' => 'Green Valley Properties',
                'block_type_id' => $blockTypes->where('name', 'Mixed Use Development')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '789 Riverside Drive',
                'address2' => 'Building A',
                'address3' => 'Chicago, IL 60601',
                'country_id' => 1,
                'state_id' => 13,
                'car_spaces' => 200,
                'inspection_count' => 4,
                'no_of_units' => 180,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Tech Hub Center',
                'management_company' => 'Innovation Properties',
                'block_type_id' => $blockTypes->where('name', 'Office Building')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '321 Innovation Way',
                'address2' => 'Tech District',
                'address3' => 'San Francisco, CA 94105',
                'country_id' => 1,
                'state_id' => 5,
                'car_spaces' => 120,
                'inspection_count' => 1,
                'no_of_units' => 60,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Shopping Mall Complex',
                'management_company' => 'Retail Management Corp',
                'block_type_id' => $blockTypes->where('name', 'Retail Complex')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '555 Commerce Street',
                'address2' => 'Mall Plaza',
                'address3' => 'Miami, FL 33101',
                'country_id' => 1,
                'state_id' => 9,
                'car_spaces' => 500,
                'inspection_count' => 2,
                'no_of_units' => 85,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Industrial Park Warehouse',
                'management_company' => 'Industrial Solutions Inc',
                'block_type_id' => $blockTypes->where('name', 'Industrial Building')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '999 Industrial Boulevard',
                'address2' => 'Unit 7',
                'address3' => 'Houston, TX 77001',
                'country_id' => 1,
                'state_id' => 43,
                'car_spaces' => 50,
                'inspection_count' => 1,
                'no_of_units' => 12,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Luxury Hotel & Resort',
                'management_company' => 'Hospitality Management Group',
                'block_type_id' => $blockTypes->where('name', 'Hotel')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '777 Luxury Lane',
                'address2' => 'Beach Front',
                'address3' => 'Miami Beach, FL 33139',
                'country_id' => 1,
                'state_id' => 9,
                'car_spaces' => 300,
                'inspection_count' => 5,
                'no_of_units' => 250,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'University Campus Building',
                'management_company' => 'Educational Facilities Management',
                'block_type_id' => $blockTypes->where('name', 'Educational Institution')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '888 University Avenue',
                'address2' => 'Campus East',
                'address3' => 'Boston, MA 02101',
                'country_id' => 1,
                'state_id' => 22,
                'car_spaces' => 100,
                'inspection_count' => 2,
                'no_of_units' => 40,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Medical Center Complex',
                'management_company' => 'Healthcare Facilities Management',
                'block_type_id' => $blockTypes->where('name', 'Healthcare Facility')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '444 Health Street',
                'address2' => 'Medical District',
                'address3' => 'Phoenix, AZ 85001',
                'country_id' => 1,
                'state_id' => 3,
                'car_spaces' => 150,
                'inspection_count' => 3,
                'no_of_units' => 75,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
            [
                'name' => 'Government Office Complex',
                'management_company' => 'Public Facilities Management',
                'block_type_id' => $blockTypes->where('name', 'Government Building')->first()->id,
                'user_id' => $users->first()->id,
                'address1' => '111 Government Plaza',
                'address2' => 'Administrative Wing',
                'address3' => 'Washington, DC 20001',
                'country_id' => 1,
                'state_id' => 8,
                'car_spaces' => 80,
                'inspection_count' => 1,
                'no_of_units' => 35,
                'created_by' => $users->first()->id,
                'updated_by' => $users->first()->id,
            ],
        ];

        foreach ($blocks as $blockData) {
            Block::create($blockData);
        }

        $this->command->info('BlockSeeder: ' . count($blocks) . ' blocks created successfully!');
    }
}
