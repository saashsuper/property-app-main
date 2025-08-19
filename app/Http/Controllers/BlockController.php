<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User; // Added this import for the new edit method

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Block::with(['blockType', 'user', 'creator', 'units'])->active();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('management_company', 'like', "%{$search}%")
                  ->orWhere('address1', 'like', "%{$search}%")
                  ->orWhere('address2', 'like', "%{$search}%")
                  ->orWhere('address3', 'like', "%{$search}%")
                  ->orWhereHas('blockType', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('creator', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $blocks = $query->orderBy('created_at', 'desc')->paginate(10);

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['user_id'] = Auth::id();
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        
        // Map new field names to existing database columns
        $data['address1'] = $request->block_address;
        $data['address2'] = $request->management_company_address;
        $data['address3'] = null; // No longer used

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'blocks';
            
            $image->storeAs('public/' . $imagePath, $imageName);
            
            $data['image_path'] = $imagePath;
            $data['image_name'] = $imageName;
        }

        $block = Block::create($data);

        return redirect()->route('blocks.index')
            ->with('success', 'Block created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Block $block)
    {
        $block->load(['blockType', 'user', 'creator', 'buildings', 'units', 'contractors', 'issues']);
        
        return view('blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Block $block)
    {
        $blockTypes = BlockType::orderBy('name')->get();
        $countries = Country::orderBy('country_name')->get();
        $states = State::orderBy('name')->get();
        $propertyManagers = User::whereHas('userType', function($query) {
            $query->where('name', 'Property manager');
        })->orderBy('name')->get();
        $block->load([
            'blockType', 
            'user', 
            'blockManager',
            'creator', 
            'updater',
            'country',
            'state',
            'buildings',
            'units',
            'contractors',
            'issues',
            'blockVisits'
        ]);
        $blockInformation = $block->blockInformation()->with('informationType')->get();
        $blockInformationTypes = \App\Models\BlockInformationType::ordered()->get();
        $blockWorkOrders = \App\Models\BlockWorkOrder::where('block_id', $block->id)->latest()->get();
        $blockInspections = \App\Models\BlockInspection::where('block_id', $block->id)->latest()->get();
        $blockBuildingTypes = \App\Models\BlockBuildingType::orderBy('name')->get();
        $buildingTypes = \App\Models\BuildingType::orderBy('name')->get();
        $blockUnitTypes = \App\Models\BlockUnitType::orderBy('name')->get();
        $users = \App\Models\User::orderBy('name')->get();
        $contractTypes = \DB::table('block_contractor_types')->orderBy('name')->get();
        $contractors = \App\Models\User::whereHas('userType', function($q) { $q->where('name', 'Contractor'); })->orderBy('name')->get();
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
            'contractTypes',
            'contractors'
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['updated_by'] = Auth::id();
        
        // Map new field names to existing database columns
        $data['address1'] = $request->block_address;
        $data['address2'] = $request->management_company_address;
        $data['address3'] = null; // No longer used

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($block->image_path && $block->image_name) {
                Storage::delete('public/' . $block->image_path . '/' . $block->image_name);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = 'blocks';
            
            $image->storeAs('public/' . $imagePath, $imageName);
            
            $data['image_path'] = $imagePath;
            $data['image_name'] = $imageName;
        }

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
        $blockInformation = $block->blockInformation()->with('informationType')->get();
        // Return only the table body partial (no layout, no full view)
        return response()->view('blocks.tabs.partials.block-info-table', compact('blockInformation'));
    }

    /**
     * Get units for autocomplete
     */
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
}
