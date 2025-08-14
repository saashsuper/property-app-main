<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlockInformationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blockInformationTypes = [
            'Building Age',
            'Construction Material',
            'Roof Type',
            'Heating System',
            'Cooling System',
            'Parking Type',
            'Security Features',
            'Amenities',
            'Maintenance Schedule',
            'Insurance Expiry',
            'Fire Safety Rating',
            'Energy Rating',
            'Last Renovation',
            'Building Condition',
            'Accessibility Features',
            'Building Height',
            'Number of Floors',
            'Foundation Type',
            'Wall Material',
            'Window Type',
            'Door Type',
            'Electrical System',
            'Plumbing System',
            'HVAC System',
            'Fire Protection',
            'Emergency Exits',
            'Building Code Compliance',
            'Zoning Information',
            'Property Tax Information',
            'Insurance Information',
            'Maintenance History',
            'Warranty Information',
            'Building Permits',
            'Environmental Certifications',
            'Accessibility Compliance',
            'Energy Efficiency',
            'Water Conservation',
            'Waste Management',
            'Landscaping',
            'Parking Facilities',
            'Common Areas',
            'Storage Facilities',
            'Utility Connections',
            'Internet Infrastructure',
            'Security Systems',
            'Building Management System',
            'Tenant Information',
            'Lease Information',
            'Financial Information',
            'Legal Documents',
            'Inspection Reports',
            'Compliance Certificates',
            'Emergency Procedures',
            'Contact Information',
        ];

        foreach ($blockInformationTypes as $infoType) {
            DB::table('block_information_types')->insert([
                'name' => $infoType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
