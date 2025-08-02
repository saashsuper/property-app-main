<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Block::with(['blockType', 'user', 'creator'])->active();

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
        return view('blocks.create', compact('blockTypes'));
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
            'address1' => 'required|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
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
        return view('blocks.edit', compact('block', 'blockTypes'));
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
            'address1' => 'required|string|max:100',
            'address2' => 'nullable|string|max:100',
            'address3' => 'nullable|string|max:100',
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
}
