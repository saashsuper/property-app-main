<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlockUnit;

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
}
