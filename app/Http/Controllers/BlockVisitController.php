<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\BlockVisit;
use Illuminate\Http\Request;

class BlockVisitController extends Controller
{
    public function index(Request $request)
    {
        $visits = BlockVisit::with('block')->orderByDesc('created_at')->paginate(10);
        return view('block-visits.index', compact('visits'));
    }

    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        return view('block-visits.create', compact('blocks'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'ref_no' => 'required|string|max:30',
            'scheduled_date_time' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        BlockVisit::create($data);
        return redirect()->route('block-visits.index')->with('success', 'Site visit created');
    }

    public function show(BlockVisit $blockVisit)
    {
        $blockVisit->load(['block', 'images', 'results', 'team']);
        return view('block-visits.show', compact('blockVisit'));
    }

    public function edit(BlockVisit $blockVisit)
    {
        $blocks = Block::orderBy('name')->get();
        return view('block-visits.edit', compact('blockVisit','blocks'));
    }

    public function update(Request $request, BlockVisit $blockVisit)
    {
        $data = $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'ref_no' => 'required|string|max:30',
            'scheduled_date_time' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);
        $blockVisit->update($data);
        return redirect()->route('block-visits.index')->with('success', 'Site visit updated');
    }

    public function destroy(BlockVisit $blockVisit)
    {
        $blockVisit->delete();
        return back()->with('success', 'Site visit deleted');
    }
}


