<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockUnit;
use App\Models\Block;
use App\Models\BlockBuilding;
use App\Models\BlockUnitType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BlockUnitController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'block_building_id' => 'required|exists:block_buildings,id',
            'block_unit_type_id' => 'required|exists:block_unit_types,id',
            'unit_code' => 'required|string|max:50',
            'unit_name' => 'required|string|max:100',
            'owners_name' => 'nullable|string|max:100',
            'salutation' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'resident' => 'nullable|boolean',
            'address1' => 'nullable|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'zip' => 'nullable|string|max:30',
            'mobile_no' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'phone_number' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'letting_agent' => 'nullable|string|max:100',
            'misc_info' => 'nullable|string|max:255',
        ], [
            'mobile_no.regex' => 'Mobile Number must be digits, spaces, or +, -, (, ) only.',
            'phone_number.regex' => 'Phone Number must be digits, spaces, or +, -, (, ) only.'
        ]);
        $validated['resident'] = $request->has('resident') ? (bool)$request->resident : false;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        BlockUnit::create($validated);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit added successfully!']);
        }
        return redirect()->back()->with('success', 'Unit added successfully!');
    }

    public function update(Request $request, BlockUnit $blockUnit)
    {
        $validated = $request->validate([
            'block_building_id' => 'required|exists:block_buildings,id',
            'block_unit_type_id' => 'required|exists:block_unit_types,id',
            'unit_code' => 'required|string|max:50',
            'unit_name' => 'required|string|max:100',
            'owners_name' => 'nullable|string|max:100',
            'salutation' => 'nullable|string|max:10',
            'email' => 'nullable|email|max:100',
            'resident' => 'nullable|boolean',
            'address1' => 'nullable|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
            'country_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'zip' => 'nullable|string|max:30',
            'mobile_no' => 'nullable|numeric',
            'phone_number' => 'nullable|numeric',
            'letting_agent' => 'nullable|string|max:100',
            'misc_info' => 'nullable|string|max:255',
        ]);
        $validated['resident'] = $request->has('resident') ? (bool)$request->resident : false;
        $validated['updated_by'] = auth()->id();
        $blockUnit->update($validated);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit updated successfully!']);
        }
        return redirect()->back()->with('success', 'Unit updated successfully!');
    }

    public function show(BlockUnit $blockUnit)
    {
        return response()->json([
            'success' => true,
            'data' => $blockUnit
        ]);
    }

    public function destroy(BlockUnit $blockUnit)
    {
        $blockUnit->delete();
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Unit deleted successfully!']);
        }
        return redirect()->back()->with('success', 'Unit deleted successfully!');
    }

    public function createSampleExcel()
    {
        try {
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers
            $headers = [
                'Unit Code',
                'Unit Name',
                'Owner\'s Name',
                'Salutation',
                'Email',
                'Resident',
                'Mobile Number',
                'Phone Number',
                'Letting Agent',
                'Miscellaneous Info'
            ];
            
            // Add headers to first row
            foreach ($headers as $index => $header) {
                $column = chr(65 + $index); // A, B, C, etc.
                $sheet->setCellValue($column . '1', $header);
                
                // Style headers
                $sheet->getStyle($column . '1')->getFont()->setBold(true);
                $sheet->getStyle($column . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle($column . '1')->getFill()->getStartColor()->setRGB('E2E8F0');
            }
            
            // Sample data
            $sampleData = [
                [
                    'A101',
                    'Apartment 101',
                    'John Doe',
                    'Mr.',
                    'john.doe@example.com',
                    'Yes',
                    '+1234567890',
                    '+1234567891',
                    'ABC Properties',
                    'Sample information for unit A101'
                ],
                [
                    'A102',
                    'Apartment 102',
                    'Jane Smith',
                    'Ms.',
                    'jane.smith@example.com',
                    'No',
                    '+1234567892',
                    '+1234567893',
                    'XYZ Properties',
                    'Sample information for unit A102'
                ],
                [
                    'B201',
                    'Apartment 201',
                    'Mike Johnson',
                    'Mr.',
                    'mike.johnson@example.com',
                    'Yes',
                    '+1234567894',
                    '+1234567895',
                    'DEF Properties',
                    'Sample information for unit B201'
                ],
                [
                    'B202',
                    'Apartment 202',
                    'Sarah Wilson',
                    'Ms.',
                    'sarah.wilson@example.com',
                    'No',
                    '+1234567896',
                    '+1234567897',
                    'GHI Properties',
                    'Sample information for unit B202'
                ],
                [
                    'C301',
                    'Apartment 301',
                    'David Brown',
                    'Mr.',
                    'david.brown@example.com',
                    'Yes',
                    '+1234567898',
                    '+1234567899',
                    'JKL Properties',
                    'Sample information for unit C301'
                ]
            ];
            
            // Add sample data
            foreach ($sampleData as $rowIndex => $rowData) {
                $row = $rowIndex + 2; // Start from row 2 (after headers)
                foreach ($rowData as $colIndex => $value) {
                    $column = chr(65 + $colIndex); // A, B, C, etc.
                    $sheet->setCellValue($column . $row, $value);
                }
            }
            
            // Auto-size columns
            foreach (range('A', 'J') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add borders
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Create the file path
            $filePath = storage_path('app/public/templates/unit-upload-template.xlsx');
            
            // Ensure directory exists
            $directory = dirname($filePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Save the file
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Sample Excel file created successfully at: ' . $filePath
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating sample Excel file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'unit_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
        ]);

        try {
            $block = Block::findOrFail($request->block_id);
            
            // Get the uploaded file
            $file = $request->file('unit_file');
            $extension = $file->getClientOriginalExtension();
            

            
            // Process the file based on its type
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Handle Excel files
                $data = $this->processExcelFile($file, $block);
            } else {
                // Handle CSV files
                $data = $this->processCsvFile($file, $block);
            }
            

            
            // Import the units
            $importedCount = $this->importUnits($data, $block);
            
            $response = [
                'success' => true,
                'message' => "Successfully imported {$importedCount} units!",
                'imported_count' => $importedCount
            ];
            
            // Add error information if any
            if (session()->has('import_errors')) {
                $response['errors'] = session('import_errors');
                session()->forget('import_errors');
            }
            
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error importing units: ' . $e->getMessage()
            ], 422);
        }
    }

    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
        ]);

        $block = Block::findOrFail($request->block_id);
        
        // Path to the Excel template file in public storage
        $templatePath = public_path('storage/templates/unit-upload-template.xlsx');
        
        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => 'Template file not found.'
            ], 404);
        }
        
        // Generate filename with block name
        $filename = 'unit_upload_template_' . str_replace(' ', '_', $block->name) . '_' . date('Y-m-d') . '.xlsx';
        
        // Return the Excel file as download
        return response()->download($templatePath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }



    private function processExcelFile($file, $block)
    {
        $data = [];
        
        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get the highest row and column numbers
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            

            
            // Get headers from first row
            $headers = [];
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headers[] = trim($worksheet->getCell($col . '1')->getValue());
            }
            

            
            // Process data rows (skip header row)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                $colIndex = 0;
                
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = trim($worksheet->getCell($col . $row)->getValue());
                    $rowData[] = $cellValue;
                    $colIndex++;
                }
                

                
                // Only process rows that have data
                if (!empty(array_filter($rowData))) {
                    $processedRow = [
                        'unit_code' => $rowData[0] ?? '',
                        'unit_name' => $rowData[1] ?? '',
                        'owners_name' => $rowData[2] ?? '',
                        'salutation' => $rowData[3] ?? '',
                        'email' => $rowData[4] ?? '',
                        'resident' => strtolower($rowData[5] ?? '') === 'yes' ? 1 : 0,
                        'mobile_no' => $rowData[6] ?? '',
                        'phone_number' => $rowData[7] ?? '',
                        'letting_agent' => $rowData[8] ?? '',
                        'misc_info' => $rowData[9] ?? '',
                        'building_name' => '', // No longer in template
                        'unit_type_name' => '' // No longer in template
                    ];
                    
                    $data[] = $processedRow;
                } else {
                    // Skip empty rows
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Error processing Excel file: ' . $e->getMessage());
            throw new \Exception('Error processing Excel file: ' . $e->getMessage());
        }
        
        return $data;
    }

    private function processCsvFile($file, $block)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Skip header row
        $headers = fgetcsv($handle);
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 10) {
                $data[] = [
                    'unit_code' => trim($row[0]),
                    'unit_name' => trim($row[1]),
                    'owners_name' => trim($row[2]),
                    'salutation' => trim($row[3]),
                    'email' => trim($row[4]),
                    'resident' => strtolower(trim($row[5])) === 'yes' ? 1 : 0,
                    'mobile_no' => trim($row[6]),
                    'phone_number' => trim($row[7]),
                    'letting_agent' => trim($row[8]),
                    'misc_info' => trim($row[9]),
                    'building_name' => '', // No longer in template
                    'unit_type_name' => '' // No longer in template
                ];
            }
        }
        
        fclose($handle);
        return $data;
    }

    private function importUnits($data, $block)
    {
        $importedCount = 0;
        $errors = [];
        
        foreach ($data as $index => $row) {
            try {
                // Check if unit code already exists
                $existingUnit = BlockUnit::where('block_id', $block->id)
                    ->where('unit_code', $row['unit_code'])
                    ->first();
                
                if ($existingUnit) {
                    $errors[] = "Row " . ($index + 2) . ": Unit code '{$row['unit_code']}' already exists";
                    continue; // Skip if unit code already exists
                }
                
                // Find building by name (optional)
                $building = null;
                if (!empty($row['building_name'])) {
                    $building = $block->buildings()->where('name', $row['building_name'])->first();
                    if (!$building) {
                        $errors[] = "Row " . ($index + 2) . ": Building '{$row['building_name']}' not found in this block";
                        continue; // Skip if building not found
                    }
                }
                
                // Find unit type by name (optional)
                $unitType = null;
                if (!empty($row['unit_type_name'])) {
                    $unitType = BlockUnitType::where('name', $row['unit_type_name'])->first();
                    if (!$unitType) {
                        $errors[] = "Row " . ($index + 2) . ": Unit type '{$row['unit_type_name']}' not found";
                        continue; // Skip if unit type not found
                    }
                }
                
                // Create the unit
                BlockUnit::create([
                    'block_id' => $block->id,
                    'block_building_id' => $building ? $building->id : null,
                    'block_unit_type_id' => $unitType ? $unitType->id : null,
                    'unit_code' => $row['unit_code'],
                    'unit_name' => $row['unit_name'],
                    'owners_name' => $row['owners_name'],
                    'salutation' => $row['salutation'],
                    'email' => $row['email'],
                    'resident' => $row['resident'],
                    'mobile_no' => $row['mobile_no'],
                    'phone_number' => $row['phone_number'],
                    'letting_agent' => $row['letting_agent'],
                    'misc_info' => $row['misc_info'],
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
                
                $importedCount++;
                
            } catch (\Exception $e) {
                // Log error and continue with next row
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                \Log::error('Error importing unit: ' . $e->getMessage(), $row);
                continue;
            }
        }
        
        // Store errors in session for display
        if (!empty($errors)) {
            session()->flash('import_errors', $errors);
        }
        
        return $importedCount;
    }
}
