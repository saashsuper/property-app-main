<?php

namespace App\Http\Controllers;

use App\Models\BlockInformation;
use App\Models\BlockInformationType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BlockInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blockInformation = BlockInformation::with(['block', 'informationType', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('block-information.index', compact('blockInformation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blockInformationTypes = BlockInformationType::active()->ordered()->get();
        return view('block-information.create', compact('blockInformationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'block_id' => 'required|exists:blocks,id',
            'information_type_id' => 'required|exists:block_information_types,id',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockInformation = BlockInformation::create([
                'block_id' => $request->block_id,
                'information_type_id' => $request->information_type_id,
                'description' => $request->description,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block information added successfully',
                'data' => $blockInformation->load(['informationType'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding block information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockInformation $blockInformation)
    {
        $blockInformation->load(['block', 'informationType', 'creator', 'updater']);
        return view('block-information.show', compact('blockInformation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockInformation $blockInformation): JsonResponse
    {
        try {
            $blockInformation->load(['informationType']);
            
            return response()->json([
                'success' => true,
                'data' => $blockInformation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading block information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockInformation $blockInformation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'information_type_id' => 'required|exists:block_information_types,id',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $blockInformation->update([
                'information_type_id' => $request->information_type_id,
                'description' => $request->description,
                'updated_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Block information updated successfully',
                'data' => $blockInformation->load(['informationType'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating block information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockInformation $blockInformation): JsonResponse
    {
        try {
            $blockInformation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Block information deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting block information: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get block information for a specific block
     */
    public function getBlockInformation($blockId): JsonResponse
    {
        try {
            $blockInformation = BlockInformation::where('block_id', $blockId)
                ->with(['informationType', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $blockInformation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block information: ' . $e->getMessage()
            ], 500);
        }
    }
}
