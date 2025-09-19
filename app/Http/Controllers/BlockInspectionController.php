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
    public function show(Request $request, BlockInspection $blockInspection)
    {
        $blockInspection->load(['block', 'creator', 'inspectionTeams.user', 'inspectionAssets.buildingAsset', 'inspectionAssets.inspectionValue']);
        
        // Return JSON when requested (AJAX/Accept headers)
        if ($request->ajax() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            $data = [
                'id' => $blockInspection->id,
                'ref_no' => $blockInspection->ref_no,
                'scheduled_date_time' => $blockInspection->scheduled_date_time,
                'start_date_time' => $blockInspection->start_date_time,
                'end_date_time' => $blockInspection->end_date_time,
                'notes' => $blockInspection->notes,
                'job_status_id' => $blockInspection->job_status_id,
                'created_by' => $blockInspection->created_by,
                // Relations in camelCase to match frontend expectations
                'creator' => $blockInspection->creator,
                'block' => $blockInspection->block,
                'inspectionTeams' => $blockInspection->inspectionTeams->map(function($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'is_lead' => (bool) $member->is_lead,
                        'role' => $member->role,
                        'user' => $member->user,
                    ];
                })->values(),
                // Also provide with snake_case for backward compatibility
                'inspection_teams' => $blockInspection->inspectionTeams->map(function($member) {
                    return [
                        'id' => $member->id,
                        'user_id' => $member->user_id,
                        'is_lead' => (bool) $member->is_lead,
                        'role' => $member->role,
                        'user' => $member->user,
                    ];
                })->values(),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
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
            'notes' => 'nullable|string|max:500',
            'job_status_id' => 'nullable|integer|exists:job_statuses,id',
        ]);

        // Only allow status updates if the status allows it
        $jobStatus = null;
        if ($request->filled('job_status_id')) {
            $jobStatus = \App\Models\JobStatus::find($request->job_status_id);
            if (!$jobStatus || !$jobStatus->is_updated) {
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This status cannot be updated through edit.'
                    ], 422);
                }
                return redirect()->back()->withErrors(['job_status_id' => 'This status cannot be updated through edit.']);
            }
        }

        $updateData = [
            'scheduled_date_time' => $request->scheduled_date_time,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ];

        // Only update status if provided and valid
        if ($jobStatus) {
            $updateData['job_status_id'] = $jobStatus->id;
        }

        $blockInspection->update($updateData);

        // Update team member (single user for modal)
        $blockInspection->inspectionTeams()->delete();
        $blockInspection->inspectionTeams()->create([
            'user_id' => $request->user_id,
            'role' => 'Lead Inspector',
            'is_lead' => true,
        ]);

        // Load the updated inspection with relationships
        $blockInspection->load(['block', 'creator', 'inspectionTeams.user']);

        // Check if request expects JSON (AJAX request)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Inspection updated successfully!',
                'inspection' => $blockInspection,
                'status_text' => $blockInspection->status_text,
                'status_color' => $blockInspection->status_color
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

    /**
     * Get inspections for a specific block.
     */
    public function getBlockInspections(Block $block)
    {
        $inspections = BlockInspection::where('block_id', $block->id)
            ->with(['creator', 'inspectionTeams.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($inspection) {
                return [
                    'id' => $inspection->id,
                    'ref_no' => $inspection->ref_no,
                    'scheduled_date_time' => $inspection->scheduled_date_time,
                    'start_date_time' => $inspection->start_date_time,
                    'end_date_time' => $inspection->end_date_time,
                    'notes' => $inspection->notes,
                    'job_status_id' => $inspection->job_status_id,
                    'created_at' => $inspection->created_at,
                    'updated_at' => $inspection->updated_at,
                    'creator' => $inspection->creator,
                    'inspection_teams' => $inspection->inspectionTeams->map(function($member) {
                        return [
                            'id' => $member->id,
                            'user_id' => $member->user_id,
                            'is_lead' => (bool) $member->is_lead,
                            'role' => $member->role,
                            'user' => $member->user,
                        ];
                    })->values(),
                    'inspectionTeams' => $inspection->inspectionTeams->map(function($member) {
                        return [
                            'id' => $member->id,
                            'user_id' => $member->user_id,
                            'is_lead' => (bool) $member->is_lead,
                            'role' => $member->role,
                            'user' => $member->user,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $inspections
        ]);
    }
}
