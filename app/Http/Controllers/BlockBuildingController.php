<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockBuilding;
use App\Models\BlockBuildingType;

class BlockBuildingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'building_type_id' => 'required|exists:block_building_types,id',
            'building_name' => 'required|string|max:100',
            'no_of_floors' => 'required|integer|min:1',
            'roof_type' => 'required|string|max:100',
            'no_lift' => 'required|integer|min:0',
        ]);
        $building = BlockBuilding::create([
            'block_id' => $validated['block_id'],
            'building_type_id' => $validated['building_type_id'],
            'name' => $validated['building_name'],
            'floor_no' => $validated['no_of_floors'],
            'roof_type' => $validated['roof_type'],
            'no_lift' => $validated['no_lift'],
        ]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Building added successfully!', 'data' => $building]);
        }
        return redirect()->back()->with('success', 'Building added successfully!');
    }

    public function update(Request $request, BlockBuilding $blockBuilding)
    {
        $validated = $request->validate([
            'building_type_id' => 'required|exists:block_building_types,id',
            'building_name' => 'required|string|max:100',
            'no_of_floors' => 'required|integer|min:1',
            'roof_type' => 'required|string|max:100',
            'no_lift' => 'required|integer|min:0',
        ]);
        $blockBuilding->update([
            'building_type_id' => $validated['building_type_id'],
            'name' => $validated['building_name'],
            'floor_no' => $validated['no_of_floors'],
            'roof_type' => $validated['roof_type'],
            'no_lift' => $validated['no_lift'],
        ]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Building updated successfully!', 'data' => $blockBuilding]);
        }
        return redirect()->back()->with('success', 'Building updated successfully!');
    }

    public function destroy(BlockBuilding $blockBuilding)
    {
        $blockBuilding->delete();
        return redirect()->back()->with('success', 'Building deleted successfully!');
    }

    public function show(BlockBuilding $blockBuilding)
    {
        return response()->json([
            'success' => true,
            'data' => $blockBuilding
        ]);
    }

    /**
     * Get block buildings for a specific block
     */
    public function getBlockBuildings($blockId)
    {
        try {
            $buildings = BlockBuilding::where('block_id', $blockId)
                ->with(['buildingType'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($building) {
                    return [
                        'id' => $building->id,
                        'building_type_name' => $building->buildingType->name ?? 'N/A',
                        'name' => $building->name,
                        'floor_no' => $building->floor_no,
                        'roof_type' => $building->roof_type,
                        'no_lift' => $building->no_lift,
                        'created_at' => $building->created_at,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $buildings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching block buildings: ' . $e->getMessage()
            ], 500);
        }
    }
}
