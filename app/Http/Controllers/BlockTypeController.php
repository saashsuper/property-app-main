<?php

namespace App\Http\Controllers;

use App\Models\BlockType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlockTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockType::withCount('blocks');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $blockTypes = $query->orderBy('name')->paginate(15);

        return view('block-types.index', compact('blockTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('block-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:block_types,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BlockType::create($request->all());

        return redirect()->route('block-types.index')
            ->with('success', 'Block type created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockType $blockType)
    {
        $blockType->load(['blocks' => function($query) {
            $query->active()->orderBy('name');
        }]);

        return view('block-types.show', compact('blockType'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockType $blockType)
    {
        return view('block-types.edit', compact('blockType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockType $blockType)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:block_types,name,' . $blockType->id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $blockType->update($request->all());

        return redirect()->route('block-types.index')
            ->with('success', 'Block type updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockType $blockType)
    {
        // Check if block type is being used
        if ($blockType->blocks()->count() > 0) {
            return redirect()->route('block-types.index')
                ->with('error', 'Cannot delete block type that is being used by blocks.');
        }

        $blockType->delete();

        return redirect()->route('block-types.index')
            ->with('success', 'Block type deleted successfully!');
    }

    /**
     * Get block types for API/JSON response.
     */
    public function getBlockTypes()
    {
        $blockTypes = BlockType::withCount('blocks')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $blockTypes
        ]);
    }
}
