<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockContractor;
use Illuminate\Support\Facades\Auth;

class BlockContractorController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'contractor_type_id' => 'required|exists:block_contractor_types,id',
            'contractor_id' => 'required|exists:users,id',
            'default_contractor' => 'nullable|boolean',
        ]);

        $status = $request->input('default_contractor') ? 1 : 0;

        BlockContractor::create([
            'block_id' => $request->block_id,
            'contractor_type_id' => $request->contractor_type_id,
            'contractor_id' => $request->contractor_id,
            'status' => $status,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Contractor added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $contractor = BlockContractor::findOrFail($id);
        $request->validate([
            'contractor_type_id' => 'required|exists:block_contractor_types,id',
            'contractor_id' => 'required|exists:users,id',
            'default_contractor' => 'nullable|boolean',
        ]);
        $status = $request->input('default_contractor') ? 1 : 0;
        $contractor->update([
            'contractor_type_id' => $request->contractor_type_id,
            'contractor_id' => $request->contractor_id,
            'status' => $status,
            'updated_by' => Auth::id(),
        ]);
        return response()->json(['success' => true, 'message' => 'Contractor updated successfully!']);
    }

    public function destroy($id)
    {
        $contractor = BlockContractor::findOrFail($id);
        $contractor->deleted_by = Auth::id();
        $contractor->save();
        $contractor->delete();
        return response()->json(['success' => true, 'message' => 'Contractor deleted successfully!']);
    }

    public function show($id)
    {
        $contractor = BlockContractor::findOrFail($id);
        return response()->json([
            'success' => true,
            'contractor' => [
                'id' => $contractor->id,
                'contractor_type_id' => $contractor->contractor_type_id,
                'contractor_id' => $contractor->contractor_id,
                'status' => $contractor->status,
            ]
        ]);
    }

    /**
     * Get block contractors for a specific block
     */
    public function getBlockContractors($blockId)
    {
        try {
            $contractors = BlockContractor::where('block_id', $blockId)
                ->with(['contractorType', 'contractor'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($contractor) {
                    return [
                        'id' => $contractor->id,
                        'contractor_type_name' => $contractor->contractorType->name ?? 'N/A',
                        'contractor_name' => $contractor->contractor->name ?? 'N/A',
                        'status' => $contractor->status,
                        'created_at' => $contractor->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $contractors
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block contractors: ' . $e->getMessage()
            ], 500);
        }
    }
}
