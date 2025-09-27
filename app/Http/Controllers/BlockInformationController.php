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
        $blockInformationTypes = BlockInformationType::ordered()->get();
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

        // Check for duplicates separately after basic validation
        if (!$validator->fails()) {
            $duplicateCheck = \App\Models\BlockInformation::where('block_id', $request->block_id)
                ->where('information_type_id', $request->information_type_id)
                ->exists();
                
            if ($duplicateCheck) {
                return response()->json([
                    'success' => false,
                    'message' => 'This information type has already been added for this block.',
                    'errors' => ['information_type_id' => ['This information type has already been added for this block.']]
                ], 422);
            }
        }

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
    public function show(BlockInformation $blockInformation): JsonResponse
    {
        try {
            $blockInformation->load(['informationType', 'creator']);

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
            'information_type_id' => [
                'required',
                'exists:block_information_types,id',
                // Prevent duplicate for the same block (exclude current record)
                function ($attribute, $value, $fail) use ($request, $blockInformation) {
                    if (\App\Models\BlockInformation::where('block_id', $blockInformation->block_id)
                        ->where('information_type_id', $value)
                        ->where('id', '!=', $blockInformation->id)
                        ->exists()) {
                        $fail('This information type has already been added for this block.');
                    }
                }
            ],
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
            $blockInformation->update(['deleted_by' => Auth::id()]);
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

    /**
     * Get block information by block ID (for AJAX requests)
     */
    public function getByBlock($blockId): JsonResponse
    {
        try {
            $blockInformation = BlockInformation::where('block_id', $blockId)
                ->with(['informationType', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($info) {
                    return [
                        'id' => $info->id,
                        'information_type_id' => $info->information_type_id,
                        'information_type_name' => $info->informationType->name ?? 'N/A',
                        'description' => $info->description,
                        'created_at' => $info->created_at,
                        'updated_at' => $info->updated_at,
                        'creator_name' => $info->creator->name ?? 'N/A',
                    ];
                });

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
