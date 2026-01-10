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
use PhpOffice\PhpSpreadsheet\Exception as PhpSpreadsheetException;

class BlockUnitController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'block_id' => 'required|exists:blocks,id',
            'block_building_id' => 'required|exists:block_buildings,id',
            'block_unit_type_id' => 'required|exists:block_unit_types,id',
            'unit_code' => 'required|string|max:50',
            'unit_name' => 'required|string|max:100',
            'owners_name' => 'nullable|string|max:100',
            'salutation' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'resident' => 'required|boolean',
            'address1' => 'nullable|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
            'country_id' => 'nullable|integer|exists:countries,id',
            'state_id' => 'nullable|integer|exists:states,id',
            'zip' => 'nullable|string|max:30',
            'mobile_no' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'phone_number' => 'nullable|regex:/^[0-9+\-() ]+$/|max:20',
            'letting_agent' => 'nullable|string|max:100',
            'misc_info' => 'nullable|string|max:255',
        ];

        // If resident is "No" (0), make address fields required
        if ($request->resident === '0' || $request->resident === 0) {
            $rules['address1'] = 'required|string|max:100';
            $rules['country_id'] = 'required|integer|exists:countries,id';
            $rules['state_id'] = 'required|integer|exists:states,id';
        }

        $validated = $request->validate($rules, [
            'mobile_no.regex' => 'Mobile Number must be digits, spaces, or +, -, (, ) only.',
            'phone_number.regex' => 'Phone Number must be digits, spaces, or +, -, (, ) only.',
            'address1.required' => 'Address Line 1 is required when Resident is No.',
            'country_id.required' => 'Country is required when Resident is No.',
            'state_id.required' => 'County/State is required when Resident is No.',
        ]);
        $validated['resident'] = $request->has('resident') ? (bool)$request->resident : false;
        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();
        $validated['status'] = BlockUnit::STATUS_ACTIVE; // Set default status to active
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
            'salutation' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'resident' => 'required|boolean',
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
        // Load all relationships
        $blockUnit->load(['building', 'unitType', 'block', 'country', 'state']);
        
        // If AJAX request, return JSON for modal
        if (request()->ajax() || request()->wantsJson()) {
            // Resolve country/state names if present
            $countryName = null;
            $stateName = null;
            if ($blockUnit->country_id) {
                $country = \App\Models\Country::find($blockUnit->country_id);
                $countryName = $country ? $country->country_name : null;
            }
            if ($blockUnit->state_id) {
                $state = \App\Models\State::find($blockUnit->state_id);
                $stateName = $state ? $state->name : null;
            }
            $data = $blockUnit->toArray();
            // Attach friendly names and relation aliases expected by frontend
            $data['building'] = $blockUnit->building ? [
                'id' => $blockUnit->building->id,
                'name' => $blockUnit->building->name,
            ] : null;
            $data['unit_type'] = $blockUnit->unitType ? [
                'id' => $blockUnit->unitType->id,
                'name' => $blockUnit->unitType->name,
            ] : null;
            $data['country_name'] = $countryName;
            $data['state_name'] = $stateName;
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        
        // Load active issues (status: Created/Open=1, In Progress=2)
        $activeIssues = \App\Models\BlockIssue::where('block_unit_id', $blockUnit->id)
            ->whereIn('issue_status_id', [1, 2])
            ->with(['priority', 'issueStatus', 'issueType', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Load active work orders (status = 1 for active)
        $activeWorkOrders = \App\Models\BlockWorkOrder::where('block_unit_id', $blockUnit->id)
            ->where('status', 1)
            ->with(['priority', 'contractor', 'blockIssue'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check if unit has any issues (for archive/delete logic)
        $hasIssues = $blockUnit->hasIssues();
        $issuesCount = $blockUnit->issues()->count();
        
        // Return full page view
        return view('block-units.show', compact('blockUnit', 'activeIssues', 'activeWorkOrders', 'hasIssues', 'issuesCount'));
    }

    /**
     * Remove the specified resource from storage.
     * 
     * If unit has no issues, it will be permanently deleted.
     * If unit has issues, it will be archived (soft deleted with archived status).
     */
    public function destroy(BlockUnit $blockUnit)
    {
        // Check if unit has any related issues
        if ($blockUnit->hasIssues()) {
            // Unit has issues - only archive it
            $blockUnit->archive();

            $message = 'Unit has been archived because it contains related issues.';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->back()->with('success', $message);
        } else {
            // Unit has no issues - permanently delete
            $blockUnit->forceDelete();

            $message = 'Unit permanently deleted successfully!';
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => $message]);
            }
            return redirect()->back()->with('success', $message);
        }
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
                'Miscellaneous Info',
                'Address Line 1',
                'Address Line 2',
                'Address Line 3',
                'Zip/EirCode'
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
                    'Sample information for unit A101',
                    '', // Address Line 1 - empty for residents
                    '', // Address Line 2 - empty for residents
                    '', // Address Line 3 - empty for residents
                    ''  // Zip/EirCode - empty for residents
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
                    'Sample information for unit A102',
                    '123 Main Street',
                    'Apt 102',
                    'Dublin 2',
                    'D02 XY12'
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
                    'Sample information for unit B201',
                    '', // Address Line 1 - empty for residents
                    '', // Address Line 2 - empty for residents
                    '', // Address Line 3 - empty for residents
                    ''  // Zip/EirCode - empty for residents
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
                    'Sample information for unit B202',
                    '456 Oak Avenue',
                    'Unit 202',
                    'Cork City',
                    'T12 AB34'
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
                    'Sample information for unit C301',
                    '', // Address Line 1 - empty for residents
                    '', // Address Line 2 - empty for residents
                    '', // Address Line 3 - empty for residents
                    ''  // Zip/EirCode - empty for residents
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
            foreach (range('A', 'N') as $column) {
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
        try {
            // Log the request for debugging
            \Log::info('Upload request received', [
                'block_id' => $request->input('block_id'),
                'all_input' => $request->all(),
                'file' => $request->hasFile('unit_file') ? $request->file('unit_file')->getClientOriginalName() : 'No file',
                'file_size' => $request->hasFile('unit_file') ? $request->file('unit_file')->getSize() : 0
            ]);

            $request->validate([
                'block_id' => 'required|exists:blocks,id',
                'unit_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // 5MB max
            ]);

            $block = Block::findOrFail($request->block_id);
            
            // Get the uploaded file
            $file = $request->file('unit_file');
            $extension = $file->getClientOriginalExtension();
            
            \Log::info('Processing file', ['extension' => $extension, 'size' => $file->getSize()]);
            
            // Process the file based on its type
            if (in_array($extension, ['xlsx', 'xls'])) {
                // Handle Excel files
                $data = $this->processExcelFile($file, $block);
            } else {
                // Handle CSV files
                $data = $this->processCsvFile($file, $block);
            }
            
            \Log::info('File processed', [
                'rows_count' => count($data),
                'sample_row' => $data[0] ?? null
            ]);
            
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
            
            \Log::info('Upload completed successfully', ['imported_count' => $importedCount]);
            return response()->json($response);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in upload', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Block not found', ['block_id' => $request->input('block_id')]);
            return response()->json([
                'success' => false,
                'message' => 'Block not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in upload method', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error importing units: ' . $e->getMessage()
            ], 500);
        }
    }

    public function testPhpSpreadsheet()
    {
        try {
            // Test if PhpSpreadsheet is working
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'Test');
            
            return response()->json([
                'success' => true,
                'message' => 'PhpSpreadsheet is working correctly',
                'version' => \PhpOffice\PhpSpreadsheet\Settings::getLibXmlLoaderOptions()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PhpSpreadsheet error: ' . $e->getMessage(),
                'error_details' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]
            ], 500);
        }
    }

    public function downloadTemplate($block_id)
    {
        try {
            // Validate that block_id exists
            $block = Block::findOrFail($block_id);
            
            // Load buildings for this block
            $buildings = $block->buildings()->orderBy('name')->get();
            $buildingNames = $buildings->pluck('name')->toArray();
            
            // Load all unit types
            $unitTypes = BlockUnitType::orderBy('name')->get();
            $unitTypeNames = $unitTypes->pluck('name')->toArray();
            
            \Log::info('Generating template', [
                'block_id' => $block_id,
                'buildings_count' => count($buildingNames),
                'unit_types_count' => count($unitTypeNames)
            ]);
            
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers with Building/Core and Unit Type at the beginning
            $headers = [
                'Unit Code',
                'Unit Name',
                'Building/Core',
                'Unit Type',
                'Owner\'s Name',
                'Salutation',
                'Email',
                'Resident',
                'Mobile Number',
                'Phone Number',
                'Letting Agent',
                'Miscellaneous Info',
                'Address Line 1',
                'Address Line 2',
                'Address Line 3',
                'Zip/EirCode'
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
            
            // Escape building names for use in formula (handle commas and quotes)
            $escapedBuildingNames = array_map(function($name) {
                // Replace quotes with double quotes and wrap in quotes if contains comma
                $escaped = str_replace('"', '""', $name);
                if (strpos($name, ',') !== false || strpos($name, '"') !== false) {
                    return '"' . $escaped . '"';
                }
                return $escaped;
            }, $buildingNames);
            
            // Escape unit type names for use in formula
            $escapedUnitTypeNames = array_map(function($name) {
                $escaped = str_replace('"', '""', $name);
                if (strpos($name, ',') !== false || strpos($name, '"') !== false) {
                    return '"' . $escaped . '"';
                }
                return $escaped;
            }, $unitTypeNames);
            
            // Add dropdown validation for Building/Core column (Column C)
            if (!empty($buildingNames)) {
                $buildingValidation = $sheet->getCell('C2')->getDataValidation();
                $buildingValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $buildingValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $buildingValidation->setAllowBlank(true);
                $buildingValidation->setShowInputMessage(true);
                $buildingValidation->setShowErrorMessage(true);
                $buildingValidation->setShowDropDown(true);
                $buildingValidation->setErrorTitle('Invalid Building/Core');
                $buildingValidation->setError('Please select a valid Building/Core from the dropdown list.');
                $buildingValidation->setPromptTitle('Select Building/Core');
                $buildingValidation->setPrompt('Please select a Building/Core from the dropdown list.');
                // Use comma-separated list formula
                $buildingValidation->setFormula1('"' . implode(',', $escapedBuildingNames) . '"');
                
                // Apply to all data rows (rows 2 to 1000)
                for ($row = 2; $row <= 1000; $row++) {
                    $sheet->getCell('C' . $row)->setDataValidation(clone $buildingValidation);
                }
            }
            
            // Add dropdown validation for Unit Type column (Column D)
            if (!empty($unitTypeNames)) {
                $unitTypeValidation = $sheet->getCell('D2')->getDataValidation();
                $unitTypeValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
                $unitTypeValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $unitTypeValidation->setAllowBlank(true);
                $unitTypeValidation->setShowInputMessage(true);
                $unitTypeValidation->setShowErrorMessage(true);
                $unitTypeValidation->setShowDropDown(true);
                $unitTypeValidation->setErrorTitle('Invalid Unit Type');
                $unitTypeValidation->setError('Please select a valid Unit Type from the dropdown list.');
                $unitTypeValidation->setPromptTitle('Select Unit Type');
                $unitTypeValidation->setPrompt('Please select a Unit Type from the dropdown list.');
                // Use comma-separated list formula
                $unitTypeValidation->setFormula1('"' . implode(',', $escapedUnitTypeNames) . '"');
                
                // Apply to all data rows (rows 2 to 1000)
                for ($row = 2; $row <= 1000; $row++) {
                    $sheet->getCell('D' . $row)->setDataValidation(clone $unitTypeValidation);
                }
            }
            
            // Sample data
            $sampleData = [
                [
                    'A101',
                    'Apartment 101',
                    !empty($buildingNames) ? $buildingNames[0] : '',
                    !empty($unitTypeNames) ? $unitTypeNames[0] : '',
                    'John Doe',
                    'Mr.',
                    'john.doe@example.com',
                    'Yes',
                    '+1234567890',
                    '+1234567891',
                    'ABC Properties',
                    'Sample information for unit A101',
                    '', // Address Line 1 - empty for residents
                    '', // Address Line 2 - empty for residents
                    '', // Address Line 3 - empty for residents
                    ''  // Zip/EirCode - empty for residents
                ],
                [
                    'A102',
                    'Apartment 102',
                    !empty($buildingNames) && count($buildingNames) > 1 ? $buildingNames[1] : (!empty($buildingNames) ? $buildingNames[0] : ''),
                    !empty($unitTypeNames) && count($unitTypeNames) > 1 ? $unitTypeNames[1] : (!empty($unitTypeNames) ? $unitTypeNames[0] : ''),
                    'Jane Smith',
                    'Ms.',
                    'jane.smith@example.com',
                    'No',
                    '+1234567892',
                    '+1234567893',
                    'XYZ Properties',
                    'Sample information for unit A102',
                    '123 Main Street',
                    'Apt 102',
                    'Dublin 2',
                    'D02 XY12'
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
            foreach (range('A', 'P') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add borders
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $sheet->getStyle('A1:' . $highestColumn . $highestRow)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Generate filename with block name
            $filename = 'unit_upload_template_' . str_replace(' ', '_', $block->name) . '_' . date('Y-m-d') . '.xlsx';
            
            // Create a temporary file for download
            $tempFile = tempnam(sys_get_temp_dir(), 'unit_template_');
            $writer = new Xlsx($spreadsheet);
            $writer->save($tempFile);
            
            // Return the Excel file as download with cache prevention headers
            $response = response()->download($tempFile, $filename, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);
            
            // Set cache prevention headers
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            
            return $response;
            
        } catch (\Exception $e) {
            \Log::error('Error generating template: ' . $e->getMessage(), [
                'block_id' => $block_id,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error generating template: ' . $e->getMessage()
            ], 500);
        }
    }



    private function processExcelFile($file, $block)
    {
        $data = [];
        
        try {
            \Log::info('Starting Excel file processing', [
                'file_path' => $file->getPathname(),
                'file_size' => $file->getSize(),
                'file_name' => $file->getClientOriginalName()
            ]);
            
            // Check if file exists and is readable
            if (!file_exists($file->getPathname()) || !is_readable($file->getPathname())) {
                throw new \Exception('File is not accessible or readable');
            }
            
            // Load the Excel file
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Get the highest row and column numbers
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            
            \Log::info('Excel file loaded', [
                'highest_row' => $highestRow,
                'highest_column' => $highestColumn
            ]);
            
            // Get headers from first row and create header index map
            $headers = [];
            $headerIndexMap = [];
            $colIndex = 0;
            for ($col = 'A'; $col <= $highestColumn; $col++) {
                $headerValue = trim($worksheet->getCell($col . '1')->getValue());
                $headers[] = $headerValue;
                $headerIndexMap[$headerValue] = $colIndex;
                $colIndex++;
            }
            
            \Log::info('Headers extracted', ['headers' => $headers, 'header_map' => $headerIndexMap]);
            
            // Helper function to get cell value by header name
            $getValueByHeader = function($row, $headerName, $default = '') use ($worksheet, $headerIndexMap, $highestColumn) {
                if (!isset($headerIndexMap[$headerName])) {
                    return $default;
                }
                $colIndex = $headerIndexMap[$headerName];
                $col = chr(65 + $colIndex); // Convert index to column letter
                if ($col > $highestColumn) {
                    return $default;
                }
                return trim($worksheet->getCell($col . $row)->getValue());
            };
            
            // Process data rows (skip header row)
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                
                for ($col = 'A'; $col <= $highestColumn; $col++) {
                    $cellValue = trim($worksheet->getCell($col . $row)->getValue());
                    $rowData[] = $cellValue;
                }
                
                // Only process rows that have data
                if (!empty(array_filter($rowData))) {
                    $processedRow = [
                        'unit_code' => $getValueByHeader($row, 'Unit Code', ''),
                        'unit_name' => $getValueByHeader($row, 'Unit Name', ''),
                        'building_name' => $getValueByHeader($row, 'Building/Core', ''),
                        'unit_type_name' => $getValueByHeader($row, 'Unit Type', ''),
                        'owners_name' => $getValueByHeader($row, 'Owner\'s Name', ''),
                        'salutation' => $getValueByHeader($row, 'Salutation', ''),
                        'email' => $getValueByHeader($row, 'Email', ''),
                        'resident' => strtolower($getValueByHeader($row, 'Resident', '')) === 'yes' ? 1 : 0,
                        'mobile_no' => $getValueByHeader($row, 'Mobile Number', ''),
                        'phone_number' => $getValueByHeader($row, 'Phone Number', ''),
                        'letting_agent' => $getValueByHeader($row, 'Letting Agent', ''),
                        'misc_info' => $getValueByHeader($row, 'Miscellaneous Info', ''),
                        'address1' => $getValueByHeader($row, 'Address Line 1', ''),
                        'address2' => $getValueByHeader($row, 'Address Line 2', ''),
                        'address3' => $getValueByHeader($row, 'Address Line 3', ''),
                        'zip' => $getValueByHeader($row, 'Zip/EirCode', ''),
                    ];
                    
                    $data[] = $processedRow;
                }
            }
            
            \Log::info('Excel processing completed', ['processed_rows' => count($data)]);
            
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            \Log::error('PhpSpreadsheet error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            throw new \Exception('Excel file format error: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Error processing Excel file: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Error processing Excel file: ' . $e->getMessage());
        }
        
        return $data;
    }

    private function processCsvFile($file, $block)
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        // Get header row
        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return $data;
        }
        
        // Create header index map
        $headerIndexMap = [];
        foreach ($headers as $index => $header) {
            $headerIndexMap[trim($header)] = $index;
        }
        
        // Helper function to get value by header name
        $getValueByHeader = function($row, $headerName, $default = '') use ($headerIndexMap) {
            if (!isset($headerIndexMap[$headerName])) {
                return $default;
            }
            $index = $headerIndexMap[$headerName];
            return isset($row[$index]) ? trim($row[$index]) : $default;
        };
        
        while (($row = fgetcsv($handle)) !== false) {
            // Only process rows that have data
            if (!empty(array_filter($row))) {
                $data[] = [
                    'unit_code' => $getValueByHeader($row, 'Unit Code', ''),
                    'unit_name' => $getValueByHeader($row, 'Unit Name', ''),
                    'building_name' => $getValueByHeader($row, 'Building/Core', ''),
                    'unit_type_name' => $getValueByHeader($row, 'Unit Type', ''),
                    'owners_name' => $getValueByHeader($row, 'Owner\'s Name', ''),
                    'salutation' => $getValueByHeader($row, 'Salutation', ''),
                    'email' => $getValueByHeader($row, 'Email', ''),
                    'resident' => strtolower($getValueByHeader($row, 'Resident', '')) === 'yes' ? 1 : 0,
                    'mobile_no' => $getValueByHeader($row, 'Mobile Number', ''),
                    'phone_number' => $getValueByHeader($row, 'Phone Number', ''),
                    'letting_agent' => $getValueByHeader($row, 'Letting Agent', ''),
                    'misc_info' => $getValueByHeader($row, 'Miscellaneous Info', ''),
                    'address1' => $getValueByHeader($row, 'Address Line 1', ''),
                    'address2' => $getValueByHeader($row, 'Address Line 2', ''),
                    'address3' => $getValueByHeader($row, 'Address Line 3', ''),
                    'zip' => $getValueByHeader($row, 'Zip/EirCode', ''),
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
        
        \Log::info('Starting unit import', [
            'block_id' => $block->id,
            'total_rows' => count($data)
        ]);
        
        foreach ($data as $index => $row) {
            try {
                \Log::debug('Processing row', [
                    'row_index' => $index + 2,
                    'unit_code' => $row['unit_code'] ?? '',
                    'building_name' => $row['building_name'] ?? '',
                    'unit_type_name' => $row['unit_type_name'] ?? ''
                ]);
                // Check if unit code already exists
                $existingUnit = BlockUnit::where('block_id', $block->id)
                    ->where('unit_code', $row['unit_code'])
                    ->first();
                
                if ($existingUnit) {
                    $errors[] = "Row " . ($index + 2) . ": Unit code '{$row['unit_code']}' already exists";
                    continue; // Skip if unit code already exists
                }
                
                // Find building by name (required)
                if (empty($row['building_name'])) {
                    $errors[] = "Row " . ($index + 2) . ": Building/Core is required";
                    continue;
                }
                
                // Try exact match first, then case-insensitive match
                $building = $block->buildings()
                    ->where('name', $row['building_name'])
                    ->first();
                
                if (!$building) {
                    // Try case-insensitive match
                    $building = $block->buildings()
                        ->whereRaw('LOWER(name) = ?', [strtolower(trim($row['building_name']))])
                        ->first();
                }
                
                if (!$building) {
                    $availableBuildings = $block->buildings()->pluck('name')->implode(', ');
                    $errors[] = "Row " . ($index + 2) . ": Building/Core '{$row['building_name']}' not found in this block. Available: " . ($availableBuildings ?: 'None');
                    continue; // Skip if building not found
                }
                
                // Find unit type by name (required)
                if (empty($row['unit_type_name'])) {
                    $errors[] = "Row " . ($index + 2) . ": Unit Type is required";
                    continue;
                }
                
                // Try exact match first, then case-insensitive match
                $unitType = BlockUnitType::where('name', $row['unit_type_name'])->first();
                
                if (!$unitType) {
                    // Try case-insensitive match
                    $unitType = BlockUnitType::whereRaw('LOWER(name) = ?', [strtolower(trim($row['unit_type_name']))])->first();
                }
                
                if (!$unitType) {
                    $availableUnitTypes = BlockUnitType::pluck('name')->implode(', ');
                    $errors[] = "Row " . ($index + 2) . ": Unit type '{$row['unit_type_name']}' not found. Available: " . ($availableUnitTypes ?: 'None');
                    continue; // Skip if unit type not found
                }
                
                // Only process address data if Resident = "No" (0)
                $country = null;
                $state = null;
                
                if ($row['resident'] == 0) {
                    // Find country by name (required when resident = No)
                    if (!empty($row['country_name'])) {
                        $country = \App\Models\Country::where('country_name', $row['country_name'])->first();
                        if (!$country) {
                            $errors[] = "Row " . ($index + 2) . ": Country '{$row['country_name']}' not found";
                            continue; // Skip if country not found
                        }
                    }
                    
                    // Find state by name (optional, but requires country)
                    if (!empty($row['state_name']) && $country) {
                        $state = \App\Models\State::where('name', $row['state_name'])
                            ->where('country_id', $country->id)
                            ->first();
                        if (!$state) {
                            $errors[] = "Row " . ($index + 2) . ": State '{$row['state_name']}' not found for country '{$row['country_name']}'";
                            continue; // Skip if state not found
                        }
                    }
                }
                
                // Prepare unit data
                $unitData = [
                    'block_id' => $block->id,
                    'block_building_id' => $building->id,
                    'block_unit_type_id' => $unitType->id,
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
                ];
                
                // Only add address data if Resident = "No"
                if ($row['resident'] == 0) {
                    $unitData['address1'] = $row['address1'];
                    $unitData['address2'] = $row['address2'];
                    $unitData['address3'] = $row['address3'];
                    $unitData['country_id'] = $country ? $country->id : null;
                    $unitData['state_id'] = $state ? $state->id : null;
                    $unitData['zip'] = $row['zip'];
                }
                
                // Create the unit
                BlockUnit::create($unitData);
                
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
        
        \Log::info('Unit import completed', [
            'block_id' => $block->id,
            'imported_count' => $importedCount,
            'total_rows' => count($data),
            'error_count' => count($errors),
            'errors' => $errors
        ]);
        
        return $importedCount;
    }

    /**
     * Get block units for a specific block
     */
    public function getBlockUnits($blockId): \Illuminate\Http\JsonResponse
    {
        try {
            $units = BlockUnit::with(['building', 'unitType', 'block'])
                ->where('block_id', $blockId)
                ->active() // Only get active units
                ->orderBy('unit_code', 'asc')
                ->get()
                ->map(function ($unit) {
                    $unitArray = $unit->toArray();
                    $unitArray['building'] = $unit->building ? [
                        'id' => $unit->building->id,
                        'name' => $unit->building->name,
                    ] : null;
                    $unitArray['unit_type'] = $unit->unitType ? [
                        'id' => $unit->unitType->id,
                        'name' => $unit->unitType->name,
                    ] : null;
                    // Add issue count for determining archive vs delete
                    $unitArray['issues_count'] = $unit->issues()->count();
                    $unitArray['has_issues'] = $unitArray['issues_count'] > 0;
                    return $unitArray;
                });

            return response()->json([
                'success' => true,
                'data' => $units
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block units: ' . $e->getMessage()
            ], 500);
        }
    }

}
