<?php

namespace App\Http\Controllers;

use App\Models\BlockInspection;
use App\Models\Block;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockInspectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlockInspection::with(['block', 'creator', 'inspectionTeams.user']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ref_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('block', function ($blockQuery) use ($search) {
                      $blockQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('job_status_id', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_date_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_date_time', '<=', $request->date_to);
        }

        $inspections = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('block-inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('block-inspections.create', compact('blocks', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'scheduled_date_time' => 'required|date|after:now',
            'notes' => 'nullable|string|max:255',
            'team_members' => 'required|array|min:1',
            'team_members.*' => 'exists:users,id',
            'lead_inspector' => 'required|exists:users,id',
        ]);

        $inspection = BlockInspection::create([
            'block_id' => $request->block_id,
            'ref_no' => BlockInspection::generateRefNo(),
            'scheduled_date_time' => $request->scheduled_date_time,
            'notes' => $request->notes,
            'job_status_id' => 1, // Scheduled
            'created_by' => Auth::id(),
        ]);

        // Create team members
        $teamMembers = array_unique(array_merge($request->team_members, [$request->lead_inspector]));
        
        foreach ($teamMembers as $userId) {
            $inspection->inspectionTeams()->create([
                'user_id' => $userId,
                'role' => $userId == $request->lead_inspector ? 'Lead Inspector' : 'Inspector',
                'is_lead' => $userId == $request->lead_inspector,
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection scheduled successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlockInspection $blockInspection)
    {
        $blockInspection->load(['block', 'creator', 'inspectionTeams.user', 'inspectionAssets.buildingAsset', 'inspectionAssets.inspectionValue']);
        
        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $blockInspection
            ]);
        }
        
        return view('block-inspections.show', compact('blockInspection'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlockInspection $blockInspection)
    {
        $blocks = Block::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $blockInspection->load('inspectionTeams');
        
        return view('block-inspections.edit', compact('blockInspection', 'blocks', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlockInspection $blockInspection)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $blockInspection->update([
            'scheduled_date_time' => $request->scheduled_date_time,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Update team member (single user for modal)
        $blockInspection->inspectionTeams()->delete();
        $blockInspection->inspectionTeams()->create([
            'user_id' => $request->user_id,
            'role' => 'Inspector',
            'is_lead' => true,
        ]);

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inspection updated successfully!',
                'inspection' => $blockInspection
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlockInspection $blockInspection)
    {
        $blockInspection->update(['deleted_by' => Auth::id()]);
        $blockInspection->delete();

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inspection deleted successfully!'
            ]);
        }

        return redirect()->route('block-inspections.index')
            ->with('success', 'Inspection deleted successfully.');
    }

    /**
     * Start the inspection.
     */
    public function start(BlockInspection $blockInspection)
    {
        $blockInspection->update([
            'start_date_time' => now(),
            'job_status_id' => 2, // In Progress
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('block-inspections.show', $blockInspection)
            ->with('success', 'Inspection started successfully.');
    }

    /**
     * Complete the inspection.
     */
    public function complete(BlockInspection $blockInspection)
    {
        $blockInspection->update([
            'end_date_time' => now(),
            'job_status_id' => 3, // Completed
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('block-inspections.show', $blockInspection)
            ->with('success', 'Inspection completed successfully.');
    }

    /**
     * Store inspection from modal form.
     */
    public function storeFromModal(Request $request)
    {
        $request->validate([
            'block_id' => 'required|exists:blocks,id',
            'user_id' => 'required|exists:users,id',
            'scheduled_date_time' => 'required|date|after:now',
            'notes' => 'required|string|max:500',
        ]);

        try {
            $inspection = BlockInspection::create([
                'block_id' => $request->block_id,
                'ref_no' => BlockInspection::generateRefNo(),
                'scheduled_date_time' => $request->scheduled_date_time,
                'notes' => $request->notes,
                'job_status_id' => 1, // Scheduled
                'created_by' => Auth::id(),
            ]);

            // Create team member (single user for modal)
            $inspection->inspectionTeams()->create([
                'user_id' => $request->user_id,
                'role' => 'Inspector',
                'is_lead' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inspection scheduled successfully!',
                'inspection' => $inspection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule inspection: ' . $e->getMessage()
            ], 500);
        }
    }
}
