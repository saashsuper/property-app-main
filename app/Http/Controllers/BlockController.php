<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\Country;
use App\Models\State;
use App\Models\BlockWorkOrder;
use App\Models\BlockInspection;
use App\Models\BlockBuildingType;
use App\Models\BuildingType;
use App\Models\BlockUnitType;
use App\Models\ContactMethod;
use App\Models\JobReason;
use App\Models\BlockImage;
use Illuminate\Support\Facades\Storage;
use App\Models\JobStatus;
use App\Models\IssueType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Added this import for the new edit method

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Block::with(['blockType', 'user', 'creator', 'units', 'blockManager', 'issues', 'workOrders'])->active();


        $blocks = $query->orderBy('created_at', 'desc')->get();

        return view('blocks.index', compact('blocks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blockTypes = BlockType::orderBy('name')->get();
        $countries = Country::orderBy('country_name')->get();
        $states = State::orderBy('name')->get();
        
        // Get property managers (users with "Property manager" user type)
        $propertyManagers = User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->orderBy('name')->get();
        
        return view('blocks.create', compact('blockTypes', 'countries', 'states', 'propertyManagers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'management_company' => 'required|string|max:100',
            'block_type_id' => 'required|exists:block_types,id',
            'block_manager_id' => 'nullable|exists:users,id',
            'block_address' => 'required|string|max:500',
            'management_company_address' => 'nullable|string|max:500',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'car_spaces' => 'required|integer|min:0',
            'inspection_count' => 'nullable|integer|min:0',
            'no_of_units' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Map new field names to existing database columns
        $data['address1'] = $request->block_address;
        $data['address2'] = $request->management_company_address;
        $data['address3'] = null; // No longer used

        $block = Block::create($data);

        return redirect()->route('blocks.index')
            ->with('success', 'Block created successfully!');
    }

    /**
     * Show the block images upload page
     */
    public function showImages($id)
    {
        $block = Block::with('images.uploader')->findOrFail($id);
        
        return view('blocks.images', compact('block'));
    }

    /**
     * Upload images for a block
     */
    public function uploadImages(Request $request, $id)
    {
        $block = Block::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB per image
        ], [
            'images.*.required' => 'Please select at least one image.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image must not exceed 5MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $images = $request->file('images');
        $totalSize = 0;
        $maxTotalSize = 15 * 1024 * 1024; // 15MB in bytes
        $maxImages = 10; // Configurable maximum number of images

        // Check total size
        foreach ($images as $image) {
            $totalSize += $image->getSize();
        }

        if ($totalSize > $maxTotalSize) {
            return response()->json([
                'success' => false,
                'message' => 'Total size of all images must not exceed 15MB.'
            ], 422);
        }

        if (count($images) > $maxImages) {
            return response()->json([
                'success' => false,
                'message' => "Maximum {$maxImages} images allowed."
            ], 422);
        }

        // Check existing images count
        $existingCount = $block->images()->count();
        if ($existingCount + count($images) > $maxImages) {
            return response()->json([
                'success' => false,
                'message' => "Maximum {$maxImages} images allowed. You already have {$existingCount} images."
            ], 422);
        }

        $uploadedImages = [];
        $imagePath = 'blocks/' . $block->id;
        
        // Get the next sort order
        $nextSortOrder = $block->images()->max('sort_order') + 1;

        foreach ($images as $index => $image) {
            $storedName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Store the file
            $image->storeAs('public/' . $imagePath, $storedName);
            
            // Create database record
            $blockImage = BlockImage::create([
                'block_id' => $block->id,
                'original_name' => $image->getClientOriginalName(),
                'stored_name' => $storedName,
                'file_path' => $imagePath,
                'file_extension' => $image->getClientOriginalExtension(),
                'file_size' => $image->getSize(),
                'mime_type' => $image->getMimeType(),
                'sort_order' => $nextSortOrder + $index,
                'is_primary' => $existingCount === 0 && $index === 0, // First image is primary if no existing images
                'uploaded_by' => Auth::id(),
            ]);

            $uploadedImages[] = [
                'id' => $blockImage->id,
                'original_name' => $blockImage->original_name,
                'stored_name' => $blockImage->stored_name,
                'file_size' => $blockImage->file_size,
                'file_size_human' => $blockImage->file_size_human,
                'url' => $blockImage->url,
                'is_primary' => $blockImage->is_primary,
                'uploaded_at' => $blockImage->created_at->format('Y-m-d H:i:s')
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully.',
            'images' => $uploadedImages
        ]);
    }

    /**
     * Delete a block image
     */
    public function deleteImage(Request $request, $id)
    {
        $block = Block::findOrFail($id);
        $imageId = $request->input('image_id');
        
        if (!$imageId) {
            return response()->json([
                'success' => false,
                'message' => 'Image ID is required.'
            ], 400);
        }

        $blockImage = $block->images()->find($imageId);
        
        if (!$blockImage) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ], 404);
        }

        // Check if this is the primary image
        $isPrimary = $blockImage->is_primary;
        
        // Delete the image (this will also delete the file due to model boot method)
        $blockImage->delete();

        // If we deleted the primary image, set another image as primary
        if ($isPrimary) {
            $nextPrimary = $block->images()->first();
            if ($nextPrimary) {
                $nextPrimary->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }

    /**
     * Set an image as primary
     */
    public function setPrimaryImage(Request $request, $id)
    {
        $block = Block::findOrFail($id);
        $imageId = $request->input('image_id');
        
        if (!$imageId) {
            return response()->json([
                'success' => false,
                'message' => 'Image ID is required.'
            ], 400);
        }

        $blockImage = $block->images()->find($imageId);
        
        if (!$blockImage) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found.'
            ], 404);
        }

        // Remove primary flag from all images
        $block->images()->update(['is_primary' => false]);
        
        // Set this image as primary
        $blockImage->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Block $block)
    {
        $block->load(['blockType', 'user', 'creator', 'buildings', 'units', 'contractors', 'issues', 'images.uploader']);
        
        // Load additional data needed for the view
        $blockInformation = $block->blockInformation()->with(['informationType', 'creator', 'updater'])->get();
        $blockInformationTypes = \App\Models\BlockInformationType::ordered()->get();
        $blockWorkOrders = \App\Models\BlockWorkOrder::where('block_id', $block->id)->latest()->get();
        $blockInspections = \App\Models\BlockInspection::where('block_id', $block->id)->latest()->get();
        $blockVisits = \App\Models\BlockVisit::where('block_id', $block->id)->with(['team.user', 'createdByUser'])->latest()->get();
        
        // Load additional data for building core and other tabs
        $blockBuildingTypes = \App\Models\BlockBuildingType::orderBy('name')->get();
        $buildingTypes = \App\Models\BuildingType::orderBy('name')->get();
        $blockUnitTypes = \App\Models\BlockUnitType::orderBy('name')->get();
        // Get only Property Manager users for the dropdown
        $users = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->with('userType')->orderBy('name')->get();
        $contractTypes = DB::table('block_contractor_types')->orderBy('name')->get();
        $contractors = \App\Models\User::whereHas('userType', function($q) { 
            $q->whereIn('name', ['Contractor Admin', 'Contractor User']); 
        })->orderBy('name')->get();
        $contactMethods = \App\Models\ContactMethod::orderBy('name')->get();
        $jobReasons = \App\Models\JobReason::orderBy('name')->get();
        $jobStatuses = \App\Models\JobStatus::orderBy('name')->get();
        $issueStatuses = \App\Models\IssueStatus::ordered()->get();
        $priorities = \App\Models\Priority::ordered()->get();
        $issueTypes = \App\Models\IssueType::orderBy('name')->get();
        
        return view('blocks.show', compact(
            'block',
            'blockInformation',
            'blockInformationTypes',
            'blockWorkOrders',
            'blockInspections',
            'blockVisits',
            'blockBuildingTypes',
            'buildingTypes',
            'blockUnitTypes',
            'users',
            'contractTypes',
            'contractors',
            'contactMethods',
            'jobReasons',
            'jobStatuses',
            'issueStatuses',
            'priorities',
            'issueTypes'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Block $block)
    {
        \Log::info('EDIT METHOD CALLED for Block ID: ' . $block->id);
        $blockTypes = BlockType::orderBy('name')->get();
        $countries = Country::orderBy('country_name')->get();
        $states = State::orderBy('name')->get();
        $propertyManagers = User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->orderBy('name')->get();
        $priorities = \App\Models\Priority::ordered()->get();
        $block->load([
            'blockType', 
            'user', 
            'blockManager',
            'creator', 
            'updater',
            'country',
            'state',
            'buildings',
            'units.unitType',
            'contractors',
            'issues',
            'blockVisits.team.user',
            'blockVisits.createdByUser',
        ]);
        $blockInformation = $block->blockInformation()->with(['informationType', 'creator', 'updater'])->get();
        $blockInformationTypes = \App\Models\BlockInformationType::ordered()->get();
        $blockWorkOrders = \App\Models\BlockWorkOrder::where('block_id', $block->id)->latest()->get();
        $blockInspections = \App\Models\BlockInspection::where('block_id', $block->id)->latest()->get();
        
        // Debug: Check if status attributes are working
        \Log::info('INSPECTION STATUS DEBUG:', [
            'block_id' => $block->id,
            'inspection_count' => $blockInspections->count(),
            'sample_inspection' => $blockInspections->first() ? [
                'id' => $blockInspections->first()->id,
                'ref_no' => $blockInspections->first()->ref_no,
                'job_status_id' => $blockInspections->first()->job_status_id,
                'status_text' => $blockInspections->first()->status_text,
                'status_color' => $blockInspections->first()->status_color,
            ] : null,
        ]);
        
        $blockBuildingTypes = \App\Models\BlockBuildingType::orderBy('name')->get();
        $buildingTypes = \App\Models\BuildingType::orderBy('name')->get();
        $blockUnitTypes = \App\Models\BlockUnitType::orderBy('name')->get();
        // Get only Property Manager users for the dropdown
        $users = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->with('userType')->orderBy('name')->get();
        // Get Contractor Admin users for site visit assignments
        $siteVisitUsers = \App\Models\User::whereHas('userType', function($query) {
            $query->where('name', 'Contractor Admin');
        })->with('userType')->orderBy('name')->get();
        $contractTypes = DB::table('block_contractor_types')->orderBy('name')->get();
        $contractors = \App\Models\User::whereHas('userType', function($q) { 
            $q->whereIn('name', ['Contractor Admin', 'Contractor User']); 
        })->orderBy('name')->get();
        $contactMethods = \App\Models\ContactMethod::orderBy('name')->get();
        $jobReasons = \App\Models\JobReason::orderBy('name')->get();
        $jobStatuses = \App\Models\JobStatus::orderBy('name')->get();
        $issueStatuses = \App\Models\IssueStatus::ordered()->get();
        $priorities = \App\Models\Priority::ordered()->get();
        $issueTypes = IssueType::where('is_active', true)->orderBy('name')->get();
        // Debug: Log what we're passing to the view
        \Log::info('PASSING TO VIEW:', [
            'blockInformation_count' => $blockInformation->count(),
            'first_record_relationships' => $blockInformation->count() > 0 ? [
                'has_informationType' => $blockInformation->first()->relationLoaded('informationType'),
                'has_creator' => $blockInformation->first()->relationLoaded('creator'),
                'informationType_name' => $blockInformation->first()->informationType ? $blockInformation->first()->informationType->name : 'NULL',
                'creator_name' => $blockInformation->first()->creator ? $blockInformation->first()->creator->name : 'NULL'
            ] : 'No records'
        ]);
        
        return view('blocks.edit', compact(
            'block', 
            'blockTypes', 
            'countries', 
            'states', 
            'propertyManagers',
            'blockInformation',
            'blockInformationTypes',
            'blockInspections', 
            'blockWorkOrders',
            'blockBuildingTypes',
            'buildingTypes',
            'blockUnitTypes',
            'users',
            'siteVisitUsers',
            'contractTypes',
            'contractors',
            'contactMethods',
            'jobReasons',
            'jobStatuses',
            'issueStatuses',
            'issueTypes',
            'priorities'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Block $block)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'management_company' => 'required|string|max:100',
            'block_type_id' => 'required|exists:block_types,id',
            'block_manager_id' => 'nullable|exists:users,id',
            'block_address' => 'required|string|max:500',
            'management_company_address' => 'nullable|string|max:500',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'car_spaces' => 'required|integer|min:0',
            'inspection_count' => 'nullable|integer|min:0',
            'no_of_units' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();
        
        // Map new field names to existing database columns
        $data['address1'] = $request->block_address;
        $data['address2'] = $request->management_company_address;
        $data['address3'] = null; // No longer used

        $block->update($data);

        return redirect()->route('blocks.index')
            ->with('success', 'Block updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Block $block)
    {
        // Delete image if exists
        if ($block->image_path && $block->image_name) {
            Storage::delete('public/' . $block->image_path . '/' . $block->image_name);
        }

        $block->deleted_by = Auth::id();
        $block->save();
        $block->delete();

        return redirect()->route('blocks.index')
            ->with('success', 'Block deleted successfully!');
    }

    /**
     * Get blocks for API/JSON response.
     */
    public function getBlocks()
    {
        $blocks = Block::with(['blockType', 'user'])
            ->active()
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blocks
        ]);
    }

    /**
     * Get a specific block for API/JSON response.
     */
    public function getBlock(Block $block)
    {
        $block->load(['blockType', 'user', 'buildings', 'units', 'contractors', 'issues']);

        return response()->json([
            'success' => true,
            'data' => $block
        ]);
    }

    /**
     * Get states by country ID for dynamic dropdown
     */
    public function getStatesByCountry($countryId)
    {
        $states = State::where('country_id', $countryId)->orderBy('name')->get();
        return response()->json($states);
    }

    public function blockInformationTable(Block $block)
    {
        $blockInformation = $block->blockInformation()->with(['informationType', 'creator', 'updater'])->get();
        // Return only the table body partial (no layout, no full view)
        return response()->view('blocks.tabs.partials.block-info-table', compact('blockInformation'));
    }

    /**
     * Get units for autocomplete
     */
    public function getUnits(Block $block)
    {
        $units = $block->units()
            ->with('unitType')
            ->orderBy('unit_code')
            ->get(['id', 'unit_code', 'unit_name', 'block_unit_type_id']);

        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }

    public function getUnitsAutocomplete(Request $request, Block $block)
    {
        $query = $request->get('query', '');
        $limit = config('autocomplete.default_limit', 10);
        
        $units = $block->units()
            ->where(function($q) use ($query) {
                $q->where('unit_code', 'like', "%{$query}%")
                  ->orWhere('unit_name', 'like', "%{$query}%")
                  ->orWhere('owners_name', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get(['id', 'unit_code', 'unit_name', 'owners_name', 'email', 'mobile_no', 'phone_number'])
            ->map(function($unit) {
                return [
                    'id' => $unit->id,
                    'text' => $unit->unit_code . ' - ' . $unit->unit_name,
                    'contact_details' => $unit->owners_name ? 
                        'Owner: ' . $unit->owners_name . 
                        ($unit->email ? '\nEmail: ' . $unit->email : '') . 
                        ($unit->mobile_no ? '\nMobile: ' . $unit->mobile_no : '') . 
                        ($unit->phone_number ? '\nPhone: ' . $unit->phone_number : '') : 
                        'No contact details available'
                ];
            });

        return response()->json($units);
    }

    /**
     * Get buildings for a specific block (API endpoint)
     */
    public function getBlockBuildings(Block $block)
    {
        $buildings = $block->buildings()
                          ->select('id', 'name', 'building_type_id')
                          ->with('buildingType:id,name')
                          ->orderBy('name')
                          ->get()
                          ->map(function($building) {
                              return [
                                  'id' => $building->id,
                                  'name' => $building->name,
                                  'type' => $building->buildingType->name ?? 'N/A'
                              ];
                          });

        return response()->json($buildings);
    }
}
